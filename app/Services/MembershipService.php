<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MembershipService
{
    private const RENEWAL_START_MONTH = 3;
    private const RENEWAL_START_DAY = 10;

    /**
     * 年間登録を更新する
     *
     * - 年度は 4/1 から翌 3/31
     * - 未登録/期限切れの場合は、いつ登録しても現在年度の 3/31 まで
     * - 当年度登録済みの場合は、3/10 以降に翌年度分を更新できる
     * - 更新は 1 年度単位のみ
     *
     * - membershipsに履歴を残す
     * - users.membership_expires_at を更新
     */
    public function renew(User $user, ?string $note = null): User
    {
        $period = $this->resolveRegistrationPeriod($user, true);

        return DB::transaction(function () use ($user, $period, $note) {
            Membership::create([
                'user_id' => $user->id,
                'start_date' => $period['start_date']->toDateString(),
                'end_date' => $period['end_date']->toDateString(),
                'note' => $note,
            ]);

            $user->membership_expires_at = $period['end_date']->toDateString();
            $user->save();

            return $user->fresh();
        });
    }

    /**
     * 画面表示用に、次に登録される期間と実行可否を返す。
     *
     * @return array{
     *     available: bool,
     *     reason: string|null,
     *     start_date: \Illuminate\Support\Carbon,
     *     end_date: \Illuminate\Support\Carbon,
     *     is_next_fiscal_year: bool,
     *     renewal_starts_on: \Illuminate\Support\Carbon
     * }
     */
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
        $renewalStartsOn = $currentEnd->copy()
            ->setMonth(self::RENEWAL_START_MONTH)
            ->setDay(self::RENEWAL_START_DAY);

        $expiresAt = $user->membership_expires_at?->copy()->startOfDay();
        $isActive = $expiresAt && $expiresAt->greaterThanOrEqualTo($today);

        $start = $currentStart;
        $end = $currentEnd;
        $isNextFiscalYear = false;

        if ($isActive && $expiresAt->greaterThanOrEqualTo($currentEnd)) {
            if ($expiresAt->greaterThanOrEqualTo($nextEnd)) {
                return $this->unavailable(
                    $throw,
                    '翌年度の年間登録は既に完了しています',
                    $nextStart,
                    $nextEnd,
                    true,
                    $renewalStartsOn
                );
            }

            if ($today->lessThan($renewalStartsOn)) {
                return $this->unavailable(
                    $throw,
                    '翌年度の年間登録更新は ' . $renewalStartsOn->format('Y-m-d') . ' から可能です',
                    $nextStart,
                    $nextEnd,
                    true,
                    $renewalStartsOn
                );
            }

            $start = $nextStart;
            $end = $nextEnd;
            $isNextFiscalYear = true;
        }

        return [
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
        $year = $date->month >= 4 ? $date->year : $date->year - 1;

        return $date->copy()->setDate($year, 4, 1)->startOfDay();
    }

    private function unavailable(
        bool $throw,
        string $reason,
        $start,
        $end,
        bool $isNextFiscalYear,
        $renewalStartsOn
    ): array {
        if ($throw) {
            throw new HttpException(403, $reason);
        }

        return [
            'available' => false,
            'reason' => $reason,
            'start_date' => $start,
            'end_date' => $end,
            'is_next_fiscal_year' => $isNextFiscalYear,
            'renewal_starts_on' => $renewalStartsOn,
        ];
    }
}
