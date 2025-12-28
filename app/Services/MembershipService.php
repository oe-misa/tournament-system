<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MembershipService
{
    /**
     * 年間登録を更新する（例：1年延長）
     * - membershipsに履歴を残す
     * - users.membership_expires_at を更新
     */
    public function renew(User $user, int $years = 1, ?string $note = null): User
    {
        if ($years <= 0 || $years > 5) {
            throw new HttpException(422, '更新年数が不正です');
        }

        return DB::transaction(function () use ($user, $years, $note) {
            $today = now()->startOfDay();

            // 期限が未来ならそこから延長、切れている/未登録なら今日から
            $base = $user->membership_expires_at && $user->membership_expires_at->greaterThan($today)
                ? $user->membership_expires_at->copy()->startOfDay()
                : $today;

            $start = $base->copy();
            $end = $base->copy()->addYears($years);

            Membership::create([
                'user_id' => $user->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'note' => $note,
            ]);

            $user->membership_expires_at = $end->toDateString();
            $user->save();

            return $user->fresh();
        });
    }
}
