<?php

use App\Models\RankRequest;
use App\Models\User;

it('formats status label and handler name', function () {
    $approver = User::factory()->create();
    $rejector = User::factory()->create();
    $rank = createRank(1);

    $req = RankRequest::create([
        'user_id' => $approver->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    expect($req->statusLabel())->toBe('未処理');
    expect($req->handledByName())->toBe('-');

    $req->status = RankRequest::STATUS_APPROVED;
    $req->approved_by = $approver->id;
    $req->approved_at = now();
    $req->save();

    $req->refresh();
    expect($req->statusLabel())->toBe('承認');
    expect($req->handledByName())->toBe($approver->name);

    $req->status = RankRequest::STATUS_REJECTED;
    $req->rejected_by = $rejector->id;
    $req->rejected_at = now();
    $req->save();

    $req->refresh();
    expect($req->statusLabel())->toBe('却下');
    expect($req->handledByName())->toBe($rejector->name);
});

it('falls back for unknown status label', function () {
    $rank = createRank(1);
    $req = RankRequest::create([
        'user_id' => User::factory()->create()->id,
        'rank_id' => $rank->id,
        'status' => 'unknown',
        'requested_at' => now(),
    ]);

    expect($req->statusLabel())->toBe('unknown');
});

it('formats display date by priority', function () {
    $rank = createRank(1);
    $req = RankRequest::create([
        'user_id' => User::factory()->create()->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now()->subDays(2),
    ]);

    $req->approved_at = now()->subDay();
    $req->save();
    expect($req->displayDateYyMmDd())->toBe($req->approved_at->format('ymd'));

    $req->approved_at = null;
    $req->rejected_at = now();
    $req->save();
    expect($req->displayDateYyMmDd())->toBe($req->rejected_at->format('ymd'));

    $req->approved_at = null;
    $req->rejected_at = null;
    $req->requested_at = now()->subDays(3);
    $req->save();
    expect($req->displayDateYyMmDd())->toBe($req->requested_at->format('ymd'));
});

it('supports status scopes', function () {
    $rank = createRank(1);
    $user = User::factory()->create();

    RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);
    RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_APPROVED,
        'requested_at' => now(),
    ]);
    RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_REJECTED,
        'requested_at' => now(),
    ]);

    expect(RankRequest::pending()->count())->toBe(1);
    expect(RankRequest::approved()->count())->toBe(1);
    expect(RankRequest::rejected()->count())->toBe(1);
});
