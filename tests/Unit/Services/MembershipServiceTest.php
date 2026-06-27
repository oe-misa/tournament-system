<?php

use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipService;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

afterEach(function () {
    Carbon::setTestNow();
});

it('registers current fiscal year until march 31 when unregistered', function () {
    Carbon::setTestNow('2026-06-28 10:00:00');
    $user = User::factory()->create(['membership_expires_at' => null]);

    $updated = (new MembershipService())->renew($user);
    $membership = Membership::firstOrFail();

    expect($updated->membership_expires_at->toDateString())->toBe('2027-03-31');
    expect($membership->user_id)->toBe($user->id);
    expect($membership->start_date->toDateString())->toBe('2026-04-01');
    expect($membership->end_date->toDateString())->toBe('2027-03-31');

    Carbon::setTestNow();
});

it('registers current fiscal year until march 31 when expired', function () {
    Carbon::setTestNow('2026-03-15 10:00:00');
    $user = User::factory()->create(['membership_expires_at' => '2026-03-01']);

    $updated = (new MembershipService())->renew($user);
    $membership = Membership::firstOrFail();

    expect($updated->membership_expires_at->toDateString())->toBe('2026-03-31');
    expect($membership->user_id)->toBe($user->id);
    expect($membership->start_date->toDateString())->toBe('2025-04-01');
    expect($membership->end_date->toDateString())->toBe('2026-03-31');

    Carbon::setTestNow();
});

it('rejects next fiscal year renewal before march 10 when current fiscal year is active', function () {
    Carbon::setTestNow('2026-03-09 10:00:00');
    $user = User::factory()->create(['membership_expires_at' => '2026-03-31']);

    $this->expectException(HttpException::class);
    $this->expectExceptionMessage('翌年度の年間登録更新は 2026-03-10 から可能です');

    (new MembershipService())->renew($user);

    Carbon::setTestNow();
});

it('renews next fiscal year from march 10 when current fiscal year is active', function () {
    Carbon::setTestNow('2026-03-10 10:00:00');
    $user = User::factory()->create(['membership_expires_at' => '2026-03-31']);

    $updated = (new MembershipService())->renew($user);
    $membership = Membership::firstOrFail();

    expect($updated->membership_expires_at->toDateString())->toBe('2027-03-31');
    expect($membership->user_id)->toBe($user->id);
    expect($membership->start_date->toDateString())->toBe('2026-04-01');
    expect($membership->end_date->toDateString())->toBe('2027-03-31');

    Carbon::setTestNow();
});

it('rejects duplicate next fiscal year renewal', function () {
    Carbon::setTestNow('2026-03-10 10:00:00');
    $user = User::factory()->create(['membership_expires_at' => '2027-03-31']);

    $this->expectException(HttpException::class);
    $this->expectExceptionMessage('翌年度の年間登録は既に完了しています');

    (new MembershipService())->renew($user);

    Carbon::setTestNow();
});
