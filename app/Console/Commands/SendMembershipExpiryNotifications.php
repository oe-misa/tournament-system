<?php

namespace App\Console\Commands;

use App\Jobs\SendMembershipExpiryNotification;
use App\Models\MembershipNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class SendMembershipExpiryNotifications extends Command
{
    protected $signature = 'memberships:send-expiry-notifications';
    protected $description = 'Queue annual membership expiry notifications';

    public function handle(): int
    {
        $today = today();
        $targets = [
            [MembershipNotification::TYPE_EXPIRING_30_DAYS, '=', $today->copy()->addDays(30)],
            [MembershipNotification::TYPE_EXPIRING_7_DAYS, '=', $today->copy()->addDays(7)],
            [MembershipNotification::TYPE_EXPIRED, '<', $today],
        ];
        $queued = 0;

        foreach ($targets as [$type, $operator, $expiryDate]) {
            User::query()
                ->where('account_status', 'active')
                ->whereDate('membership_expires_at', $operator, $expiryDate)
                ->eachById(function (User $user) use ($type, &$queued) {
                    $fiscalYear = $user->membership_expires_at->month >= 4
                        ? $user->membership_expires_at->year
                        : $user->membership_expires_at->year - 1;

                    try {
                        $notification = MembershipNotification::create([
                            'user_id' => $user->id,
                            'type' => $type,
                            'fiscal_year' => $fiscalYear,
                        ]);
                    } catch (QueryException $e) {
                        return; // The unique index makes repeated scheduler runs safe.
                    }

                    SendMembershipExpiryNotification::dispatch($notification->id);
                    $queued++;
                });
        }

        $this->info("通知を {$queued} 件キューへ投入しました。");

        return self::SUCCESS;
    }
}
