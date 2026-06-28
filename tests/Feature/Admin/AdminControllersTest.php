<?php

use App\Models\Entry;
use App\Models\RankRequest;
use App\Models\Result;
use App\Models\Tournament;
use App\Models\User;

it('admin can manage tournaments', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin/tournaments')
        ->assertOk();

    $this->actingAs($admin)
        ->get('/admin/tournaments/create')
        ->assertOk();

    $this->actingAs($admin)
        ->post('/admin/tournaments', [
            'title' => 'Test',
            'description' => 'Desc',
            'event_date' => now()->toDateString(),
            'entry_deadline' => null,
            'capacity' => 10,
            'min_rank_level' => 0,
        ])
        ->assertRedirect();

    $tournament = Tournament::first();

    $this->actingAs($admin)
        ->get("/admin/tournaments/{$tournament->id}/edit")
        ->assertOk();

    $this->actingAs($admin)
        ->get("/admin/tournaments/{$tournament->id}")
        ->assertRedirect("/admin/tournaments/{$tournament->id}/edit");

    $this->actingAs($admin)
        ->put("/admin/tournaments/{$tournament->id}", [
            'title' => 'Updated',
            'description' => 'Desc',
            'event_date' => now()->toDateString(),
            'entry_deadline' => null,
            'capacity' => 20,
            'min_rank_level' => 0,
        ])
        ->assertRedirect();

    $this->actingAs($admin)
        ->delete("/admin/tournaments/{$tournament->id}")
        ->assertRedirect('/admin/tournaments');
});

it('admin can view and update results', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $tournament = Tournament::create([
        'title' => 'T',
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
        ->get("/admin/tournaments/{$tournament->id}/results")
        ->assertOk();

    $this->actingAs($admin)
        ->post("/admin/tournaments/{$tournament->id}/results", [
            'results' => [
                $user->id => [
                    'placing' => 1,
                    'score' => 100,
                    'note' => 'good',
                ],
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('results', [
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'placing' => 1,
    ]);
});

it('admin can cancel entries from tournament edit page', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => now()->addDays(5),
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $entry = Entry::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => Entry::STATUS_ENTRY,
    ]);

    $this->actingAs($admin)
        ->get("/admin/tournaments/{$tournament->id}/edit")
        ->assertOk()
        ->assertSee('エントリー一覧');

    $this->actingAs($admin)
        ->delete("/admin/tournaments/{$tournament->id}/entries/{$entry->id}")
        ->assertRedirect()
        ->assertSessionHas('status', 'エントリーをキャンセルしました');

    $entry->refresh();
    expect($entry->status)->toBe(Entry::STATUS_CANCELLED);
});

it('skips empty results rows on update', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $this->actingAs($admin)
        ->post("/admin/tournaments/{$tournament->id}/results", [
            'results' => [
                $user->id => [
                    'placing' => null,
                    'score' => null,
                    'note' => null,
                ],
            ],
        ])
        ->assertRedirect();

    $this->assertDatabaseMissing('results', [
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
    ]);
});

it('admin can approve and reject rank requests', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $rank = createRank(2);

    $request = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_PENDING,
        'requested_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get('/admin/rank-requests')
        ->assertOk();

    $this->actingAs($admin)
        ->post("/admin/rank-requests/{$request->id}/approve", [
            'admin_comment' => 'ok',
        ])
        ->assertRedirect();

    $request->refresh();
    expect($request->status)->toBe(RankRequest::STATUS_APPROVED);

    $request->status = RankRequest::STATUS_PENDING;
    $request->save();

    $this->actingAs($admin)
        ->post("/admin/rank-requests/{$request->id}/reject", [
            'admin_comment' => 'no',
        ])
        ->assertRedirect();

    $request->refresh();
    expect($request->status)->toBe(RankRequest::STATUS_REJECTED);
});

it('admin sees processed message for non-pending rank requests', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $rank = createRank(2);

    $request = RankRequest::create([
        'user_id' => $user->id,
        'rank_id' => $rank->id,
        'status' => RankRequest::STATUS_APPROVED,
        'requested_at' => now(),
    ]);

    $this->actingAs($admin)
        ->post("/admin/rank-requests/{$request->id}/approve")
        ->assertRedirect()
        ->assertSessionHas('status', 'この申請は既に処理済みです');

    $request->refresh();
    $this->actingAs($admin)
        ->post("/admin/rank-requests/{$request->id}/reject")
        ->assertRedirect()
        ->assertSessionHas('status', 'この申請は既に処理済みです');
});
