<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MembershipService
{
    private const RENEWAL_START_MONTH = 3;
    private const RENEWAL_START_DAY = 10;

    /** Create an annual-registration application. Approval is the only action that updates the user's expiry date. */
    public function request(User $user, ?string $note = null): Membership
    {
        return DB::transaction(function () use ($user, $note) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);
            $period = $this->resolveRegistrationPeriod($user, true);

            $existing = Membership::query()
                ->where('user_id', $user->id)
                ->whereDate('start_date', $period['start_date'])
                ->whereDate('end_date', $period['end_date'])
                ->first();

            if ($existing && $existing->status !== Membership::STATUS_REJECTED) {
                throw new HttpException(409, 'この年度の年間登録申請は既に存在します');
            }

            if ($existing) {
                $existing->update(['status' => Membership::STATUS_PENDING_PAYMENT, 'rejected_by' => null, 'rejected_at' => null, 'admin_comment' => null]);
                $this->audit($user->id, 'membership.reapplied', $existing, ['status']);
                $this->notify($user, '年間登録を再申請しました。振込後、管理者の入金確認をお待ちください。');
                return $existing->refresh();
            }

            $membership = Membership::create([
                'user_id' => $user->id,
                'start_date' => $period['start_date']->toDateString(),
                'end_date' => $period['end_date']->toDateString(),
                'note' => $note,
                'status' => Membership::STATUS_PENDING_PAYMENT,
            ]);
            $this->audit($user->id, 'membership.requested', $membership, ['status', 'start_date', 'end_date']);
            $this->notify($user, '年間登録を申請しました。振込後、管理者の入金確認をお待ちください。');
            return $membership;
        });
    }

    public function confirmPayment(User $admin, Membership $membership, ?string $comment = null, ?string $paymentReference = null, ?string $confirmedOn = null): Membership
    {
        return DB::transaction(function () use ($admin, $membership, $comment, $paymentReference, $confirmedOn) {
            $this->assertAdmin($admin);
            $membership = Membership::query()->lockForUpdate()->findOrFail($membership->id);

            if ($membership->status !== Membership::STATUS_PENDING_PAYMENT) {
                throw new HttpException(409, '入金確認できる状態ではありません');
            }

            $membership->update([
                'status' => Membership::STATUS_PAYMENT_CONFIRMED,
                'payment_confirmed_by' => $admin->id,
                'payment_confirmed_at' => now(),
                'payment_confirmed_on' => $confirmedOn ?? today()->toDateString(),
                'payment_reference' => $paymentReference,
                'admin_comment' => $comment,
            ]);

            $membership = $membership->refresh();
            $this->audit($admin->id, 'membership.payment_confirmed', $membership, ['status', 'payment_reference', 'payment_confirmed_on', 'payment_confirmed_by']);
            $this->notify($membership->user, '年間登録の入金を確認しました。管理者承認をお待ちください。');
            return $membership;
        });
    }

    public function approve(User $admin, Membership $membership, ?string $comment = null): Membership
    {
        return DB::transaction(function () use ($admin, $membership, $comment) {
            $this->assertAdmin($admin);
            $membership = Membership::query()->lockForUpdate()->findOrFail($membership->id);

            if ($membership->status !== Membership::STATUS_PAYMENT_CONFIRMED) {
                throw new HttpException(409, '入金確認済みの申請のみ承認できます');
            }

            $user = User::query()->lockForUpdate()->findOrFail($membership->user_id);
            $user->membership_expires_at = $membership->end_date;
            $user->save();

            $membership->update([
                'status' => Membership::STATUS_APPROVED,
                'approved_by' => $admin->id,
                'approved_at' => now(),
                'admin_comment' => $comment,
            ]);

            $membership = $membership->refresh();
            $this->audit($admin->id, 'membership.approved', $membership, ['status', 'approved_by', 'approved_at']);
            $this->notify($membership->user, '年間登録を承認しました。有効期限は ' . $membership->end_date->format('Y-m-d') . ' です。');
            return $membership;
        });
    }

    public function reject(User $admin, Membership $membership, ?string $comment = null): Membership
    {
        return DB::transaction(function () use ($admin, $membership, $comment) {
            $this->assertAdmin($admin);
            $membership = Membership::query()->lockForUpdate()->findOrFail($membership->id);

            if (!in_array($membership->status, [Membership::STATUS_PENDING_PAYMENT, Membership::STATUS_PAYMENT_CONFIRMED], true)) {
                throw new HttpException(409, '却下できる状態ではありません');
            }

            $membership->update([
                'status' => Membership::STATUS_REJECTED,
                'rejected_by' => $admin->id,
                'rejected_at' => now(),
                'admin_comment' => $comment,
            ]);

            $membership = $membership->refresh();
            $this->audit($admin->id, 'membership.rejected', $membership, ['status', 'rejected_by', 'rejected_at']);
            $this->notify($membership->user, '年間登録申請は却下されました。管理画面のコメントをご確認ください。');
            return $membership;
        });
    }

    /** @return array{available: bool, reason: string|null, start_date: \Illuminate\Support\Carbon, end_date: \Illuminate\Support\Carbon, is_next_fiscal_year: bool, renewal_starts_on: \Illuminate\Support\Carbon} */
    public function preview(User $user): array
    {
        return $this->resolveRegistrationPeriod($user, false);
    }

    private function resolveRegistrationPeriod(User $user, bool $throw): array
    {
        $today = now()->startOfDay();
        $currentStart = $this->fiscalYearStart($today);
        $currentEnd = $currentStart->copy()->addYear()->subDay();
        $nextStart = $currentStart->copy()->addYear();
        $nextEnd = $currentEnd->copy()->addYear();
        $renewalStartsOn = $currentEnd->copy()->setMonth(self::RENEWAL_START_MONTH)->setDay(self::RENEWAL_START_DAY);
        $expiresAt = $user->membership_expires_at?->copy()->startOfDay();
        $isActive = $expiresAt && $expiresAt->greaterThanOrEqualTo($today);

        $start = $currentStart;
        $end = $currentEnd;
        $isNextFiscalYear = false;

        if ($isActive && $expiresAt->greaterThanOrEqualTo($currentEnd)) {
            if ($expiresAt->greaterThanOrEqualTo($nextEnd)) {
                return $this->unavailable($throw, '翌年度の年間登録は既に完了しています', $nextStart, $nextEnd, true, $renewalStartsOn);
            }
            if ($today->lessThan($renewalStartsOn)) {
                return $this->unavailable($throw, '翌年度の年間登録申請は ' . $renewalStartsOn->format('Y-m-d') . ' から可能です', $nextStart, $nextEnd, true, $renewalStartsOn);
            }
            $start = $nextStart;
            $end = $nextEnd;
            $isNextFiscalYear = true;
        }

        return compact('start', 'end', 'isNextFiscalYear', 'renewalStartsOn') + [
            'available' => true,
            'reason' => null,
            'start_date' => $start,
            'end_date' => $end,
            'is_next_fiscal_year' => $isNextFiscalYear,
            'renewal_starts_on' => $renewalStartsOn,
        ];
    }

    private function fiscalYearStart($date)
    {
        return $date->copy()->setDate($date->month >= 4 ? $date->year : $date->year - 1, 4, 1)->startOfDay();
    }

    private function unavailable(bool $throw, string $reason, $start, $end, bool $isNextFiscalYear, $renewalStartsOn): array
    {
        if ($throw) throw new HttpException(403, $reason);

        return ['available' => false, 'reason' => $reason, 'start_date' => $start, 'end_date' => $end, 'is_next_fiscal_year' => $isNextFiscalYear, 'renewal_starts_on' => $renewalStartsOn];
    }

    private function assertAdmin(User $user): void
    {
        if (!$user->is_admin) throw new HttpException(403, '管理者のみ実行できます');
    }

    private function notify(User $user, string $message): void
    {
        try {
            Mail::raw($message, fn ($mail) => $mail->to($user->email)->subject('福岡かるた会員システム'));
        } catch (\Throwable $e) {
            Log::warning('Membership notification failed', ['user_id' => $user->id, 'exception' => $e::class]);
        }
    }

    private function audit(int $actorId, string $event, Membership $membership, array $fields): void
    {
        AuditLog::create(['actor_id' => $actorId, 'event' => $event, 'auditable_type' => Membership::class, 'auditable_id' => $membership->id, 'changed_fields' => $fields]);
    }
}
