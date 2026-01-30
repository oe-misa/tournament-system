<?php

use App\Models\Entry;
use App\Models\Membership;
use App\Models\OmikujiDraw;
use App\Models\Rank;
use App\Models\RankRequest;
use App\Models\Result;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

it('exposes model relationships', function () {
    $rank = createRank(1);
    $user = User::factory()->create(['rank_id' => $rank->id]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $entry = Entry::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => 'entry',
    ]);

    $result = Result::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'placing' => 1,
        'score' => 10,
        'note' => null,
    ]);

    $membership = Membership::create([
        'user_id' => $user->id,
        'start_date' => now()->toDateString(),
        'end_date' => now()->addYear()->toDateString(),
        'note' => null,
    ]);

    $rankRequest = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $omikuji = OmikujiDraw::create([
        'user_id' => $user->id,
        'result' => '吉',
        'drawn_on' => now()->toDateString(),
    ]);

    expect($user->rank())->toBeInstanceOf(BelongsTo::class);
    expect($user->entries())->toBeInstanceOf(HasMany::class);
    expect($user->results())->toBeInstanceOf(HasMany::class);
    expect($user->memberships())->toBeInstanceOf(HasMany::class);
    expect($user->rankRequests())->toBeInstanceOf(HasMany::class);

    expect($entry->user())->toBeInstanceOf(BelongsTo::class);
    expect($entry->tournament())->toBeInstanceOf(BelongsTo::class);

    expect($result->user())->toBeInstanceOf(BelongsTo::class);
    expect($result->tournament())->toBeInstanceOf(BelongsTo::class);

    expect($membership->user())->toBeInstanceOf(BelongsTo::class);

    expect($rankRequest->user())->toBeInstanceOf(BelongsTo::class);
    expect($rankRequest->rank())->toBeInstanceOf(BelongsTo::class);
    expect($rankRequest->requestedRank())->toBeInstanceOf(BelongsTo::class);
    expect($rankRequest->approver())->toBeInstanceOf(BelongsTo::class);
    expect($rankRequest->rejector())->toBeInstanceOf(BelongsTo::class);

    expect($tournament->entries())->toBeInstanceOf(HasMany::class);
    expect($tournament->results())->toBeInstanceOf(HasMany::class);

    expect($rank->users())->toBeInstanceOf(HasMany::class);

    expect($omikuji->user())->toBeInstanceOf(BelongsTo::class);
});
