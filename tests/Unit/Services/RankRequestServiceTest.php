<?php

use App\Models\RankRequest;
use App\Models\User;
use App\Services\RankRequestService;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('creates a rank request for higher rank', function () {
    $rank = createRank(2);
    $userRank = createRank(1);
    $user = User::factory()->create(['rank_id' => $userRank->id]);

    $service = new RankRequestService();
    $request = $service->request($user, $rank, 'note');

    expect($request->status)->toBe(RankRequest::STATUS_PENDING);
    expect($request->note)->toBe('note');
});

it('rejects request for lower rank', function () {
    $rank = createRank(1);
    $userRank = createRank(2);
    $user = User::factory()->create(['rank_id' => $userRank->id]);

    $service = new RankRequestService();

    $this->expectException(HttpException::class);
    $service->request($user, $rank);
});

it('rejects duplicate pending request', function () {
    $rank = createRank(2);
    $userRank = createRank(1);
    $user = User::factory()->create(['rank_id' => $userRank->id]);

    RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $service = new RankRequestService();

    $this->expectException(HttpException::class);
    $service->request($user, $rank);
});

it('approves a pending request', function () {
    $rank = createRank(2);
    $userRank = createRank(1);
    $user = User::factory()->create(['rank_id' => $userRank->id]);
    $admin = createAdmin();

    $request = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $service = new RankRequestService();
    $updated = $service->approve($admin, $request, 'ok');

    expect($updated->status)->toBe(RankRequest::STATUS_APPROVED);
    expect($updated->admin_comment)->toBe('ok');
    expect($user->fresh()->rank_id)->toBe($rank->id);
});

it('rejects approve by non-admin or non-pending', function () {
    $rank = createRank(2);
    $user = User::factory()->create();
    $nonAdmin = User::factory()->create(['is_admin' => false]);

    $request = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $service = new RankRequestService();

    $this->expectException(HttpException::class);
    $service->approve($nonAdmin, $request);

    $request->status = RankRequest::STATUS_APPROVED;
    $request->save();

    $this->expectException(HttpException::class);
    $service->approve(createAdmin(), $request);
});

it('rejects a pending request', function () {
    $rank = createRank(2);
    $user = User::factory()->create();
    $admin = createAdmin();

    $request = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $service = new RankRequestService();
    $updated = $service->reject($admin, $request, 'no');

    expect($updated->status)->toBe(RankRequest::STATUS_REJECTED);
    expect($updated->admin_comment)->toBe('no');
});

it('rejects non-pending request on reject', function () {
    $rank = createRank(2);
    $user = User::factory()->create();
    $admin = createAdmin();

    $request = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_APPROVED,
        'requested_at' => now(),
    ]);

    $service = new RankRequestService();

    $this->expectException(HttpException::class);
    $service->reject($admin, $request);
});
