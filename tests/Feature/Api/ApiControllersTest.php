<?php

use App\Models\Result;
use App\Models\Tournament;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Entry;

it('returns current user via api', function () {
    $rank = createRank(2);
    $user = User::factory()->create(['rank_id' => $rank->id, 'is_admin' => true]);

    Sanctum::actingAs($user);

    $this->getJson('/api/me')
        ->assertOk()
        ->assertJsonFragment(['id' => $user->id])
        ->assertJsonFragment(['is_admin' => true]);
});

it('returns tournaments via api', function () {
    $user = User::factory()->create();
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/tournaments')
        ->assertOk()
        ->assertJsonFragment(['id' => $tournament->id]);

    $this->getJson("/api/tournaments/{$tournament->id}")
        ->assertOk()
        ->assertJsonFragment(['id' => $tournament->id]);
});

it('returns results via api', function () {
    $user = User::factory()->create();
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    Result::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'placing' => 1,
        'score' => 100,
        'note' => null,
    ]);

    Sanctum::actingAs($user);

    $this->getJson('/api/results')
        ->assertOk()
        ->assertJsonFragment(['tournament_id' => $tournament->id]);
});

it('allows entry via api and handles error', function () {
    $rank = createRank(2);
    $user = User::factory()->create([
        'rank_id' => $rank->id,
        'membership_expires_at' => now()->addDay(),
    ]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    Sanctum::actingAs($user);

    $this->postJson("/api/tournaments/{$tournament->id}/entries")
        ->assertStatus(201);

    $this->assertDatabaseHas('entries', [
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
    ]);

    $expiredUser = User::factory()->create(['membership_expires_at' => now()->subDay()]);
    Sanctum::actingAs($expiredUser);

    $this->postJson("/api/tournaments/{$tournament->id}/entries")
        ->assertStatus(403);
});

it('allows cancel via api', function () {
    $rank = createRank(2);
    $user = User::factory()->create([
        'rank_id' => $rank->id,
        'membership_expires_at' => now()->addDay(),
    ]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => now()->addDays(11),
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    Entry::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => Entry::STATUS_ENTRY,
    ]);

    Sanctum::actingAs($user);

    $this->deleteJson("/api/tournaments/{$tournament->id}/entries")
        ->assertOk()
        ->assertJsonFragment(['message' => 'エントリーをキャンセルしました']);

    $this->assertDatabaseHas('entries', [
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => Entry::STATUS_CANCELLED,
    ]);
});

it('creates rank request via api and handles error', function () {
    $rank = createRank(2);
    $userRank = createRank(1);
    $user = User::factory()->create(['rank_id' => $userRank->id]);

    Sanctum::actingAs($user);

    $this->postJson('/api/rank-requests', [
        'rank_id' => $rank->id,
        'note' => 'note',
    ])->assertStatus(201);

    $this->assertDatabaseHas('rank_requests', ['user_id' => $user->id]);

    $lower = createRank(0);
    $this->postJson('/api/rank-requests', [
        'rank_id' => $lower->id,
    ])->assertStatus(422);
});
