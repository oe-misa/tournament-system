<?php

use App\Models\Entry;
use App\Models\OmikujiDraw;
use App\Models\RankRequest;
use App\Models\Result;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Carbon;

it('shows dashboard for member with omikuji status', function () {
    $user = User::factory()->create();
    Carbon::setTestNow('2026-01-30 10:00:00');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('本日のおみくじ');

    $this->actingAs($user)->post('/omikuji/draw')->assertRedirect('/dashboard');

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('結果:');

    Carbon::setTestNow();
});

it('shows admin counters on dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $rank = createRank(1);

    RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => 'pending',
        'requested_at' => now(),
    ]);

    $tournament = Tournament::create([
        'title' => 'Test Tournament',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    Entry::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => 'entry',
    ]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk()
        ->assertSee('未処理')
        ->assertSee('成績未入力');

    Result::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'placing' => 1,
        'score' => 100,
        'note' => null,
    ]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertOk();
});
