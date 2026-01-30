<?php

use App\Models\Membership;
use App\Models\User;
use App\Services\MembershipService;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('rejects invalid renewal years', function () {
    $user = User::factory()->create();
    $service = new MembershipService();

    $this->expectException(HttpException::class);
    $service->renew($user, 0);
});

it('renews membership from today when expired', function () {
    $user = User::factory()->create(['membership_expires_at' => now()->subDay()]);
    $service = new MembershipService();

    $updated = $service->renew($user, 1);

    $this->assertNotNull($updated->membership_expires_at);
    $this->assertDatabaseCount('memberships', 1);
});

it('renews membership from existing expiry when active', function () {
    $user = User::factory()->create(['membership_expires_at' => now()->addDays(10)]);
    $service = new MembershipService();

    $updated = $service->renew($user, 1);

    expect($updated->membership_expires_at->greaterThan(now()->addDays(10)))->toBeTrue();
    $this->assertDatabaseCount('memberships', 1);
});
