<?php

use App\Models\RankRequest;
use App\Models\Result;
use App\Models\Tournament;
use App\Models\User;

it('shows tournaments list and detail', function () {
    $user = User::factory()->create();
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $this->actingAs($user)->get('/tournaments')->assertOk();
    $this->actingAs($user)->get("/tournaments/{$tournament->id}")->assertOk();
});

it('shows results list', function () {
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

    $this->actingAs($user)->get('/results')->assertOk();
});

it('handles rank request create, store and history', function () {
    $rank = createRank(1);
    $user = User::factory()->create(['rank_id' => $rank->id]);

    $this->actingAs($user)->get('/rank-requests')->assertOk();

    $this->actingAs($user)
        ->post('/rank-requests', [
            'requested_rank_id' => $rank->id,
            'note' => 'note',
        ])
        ->assertRedirect('/dashboard');

    $this->actingAs($user)
        ->post('/rank-requests', [
            'requested_rank_id' => $rank->id,
        ])
        ->assertSessionHasErrors();

    $this->actingAs($user)->get('/rank-requests/history')->assertOk();
});

it('handles membership pages', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/membership/renew')->assertOk();
    $this->actingAs($user)
        ->post('/membership/renew', ['years' => 1])
        ->assertRedirect('/membership/renew');
});

it('handles membership service errors', function () {
    $user = User::factory()->create();

    app()->instance(\App\Services\MembershipService::class, new class extends \App\Services\MembershipService {
        public function renew(\App\Models\User $user, int $years = 1, ?string $note = null): \App\Models\User
        {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'error');
        }
    });

    $this->actingAs($user)
        ->post('/membership/renew', ['years' => 1])
        ->assertRedirect('/membership/renew')
        ->assertSessionHas('error', 'error');
});

it('returns rank definition json', function () {
    $rank = createRank(3);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get("/rank-definitions/{$rank->id}")
        ->assertOk()
        ->assertJsonFragment(['id' => $rank->id])
        ->assertJsonFragment(['eligible_kyus' => 'A,B級']);
});
