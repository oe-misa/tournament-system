<?php

use App\Models\Entry;
use App\Models\Tournament;
use App\Models\User;

it('allows entry via web controller when valid', function () {
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

    $this->actingAs($user)
        ->post("/tournaments/{$tournament->id}/entry")
        ->assertRedirect("/tournaments/{$tournament->id}")
        ->assertSessionHas('status');
});

it('shows error when entry is invalid', function () {
    $user = User::factory()->create(['membership_expires_at' => now()->subDay()]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $this->actingAs($user)
        ->post("/tournaments/{$tournament->id}/entry")
        ->assertRedirect("/tournaments/{$tournament->id}")
        ->assertSessionHas('error');
});

it('allows cancel via web controller', function () {
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

    $this->actingAs($user)
        ->delete("/tournaments/{$tournament->id}/entry")
        ->assertRedirect("/tournaments/{$tournament->id}")
        ->assertSessionHas('status', 'エントリーをキャンセルしました');

    $this->assertDatabaseHas('entries', [
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => Entry::STATUS_CANCELLED,
    ]);
});
