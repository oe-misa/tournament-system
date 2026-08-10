<?php

namespace App\Jobs;

use App\Models\MembershipNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMembershipExpiryNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $notificationId) {}

    public function handle(): void
    {
        $notification = MembershipNotification::with('user')->find($this->notificationId);
        $user = $notification?->user;

        if (!$user?->isActive() || !$user->membership_expires_at) {
            return;
        }

        $messages = [
            MembershipNotification::TYPE_EXPIRING_30_DAYS => '年間登録の有効期限まで30日です。更新をご検討ください。',
            MembershipNotification::TYPE_EXPIRING_7_DAYS => '年間登録の有効期限まで7日です。お早めに更新してください。',
            MembershipNotification::TYPE_EXPIRED => '年間登録の有効期限が切れています。年間登録を更新してください。',
        ];

        try {
            Mail::raw($messages[$notification->type] ?? '年間登録の有効期限をご確認ください。', function ($mail) use ($user) {
                $mail->to($user->email)->subject('年間登録の有効期限のお知らせ');
            });
        } catch (\Throwable $e) {
            Log::warning('Membership expiry notification failed', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'exception' => $e::class,
            ]);
        }
    }
}
