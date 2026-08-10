<?php

use App\Jobs\SendMembershipExpiryNotification;
use App\Models\MembershipNotification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;

afterEach(fn () => Carbon::setTestNow());

it('queues each due membership expiry notification once for active accounts', function () {
    Carbon::setTestNow('2026-08-10 09:15:00');
    Queue::fake();
    $due = User::factory()->create(['membership_expires_at' => '2026-09-09', 'account_status' => 'active']);
    $inactive = User::factory()->create(['membership_expires_at' => '2026-09-09', 'account_status' => 'inactive']);

    $this->artisan('memberships:send-expiry-notifications')->assertSuccessful();
    $this->artisan('memberships:send-expiry-notifications')->assertSuccessful();

    expect(MembershipNotification::where('user_id', $due->id)->count())->toBe(1);
    expect(MembershipNotification::where('user_id', $inactive->id)->count())->toBe(0);
    Queue::assertPushed(SendMembershipExpiryNotification::class, 1);
});
