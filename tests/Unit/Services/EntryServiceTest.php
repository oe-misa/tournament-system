<?php

use App\Models\Entry;
use App\Models\Tournament;
use App\Models\User;
use App\Services\EntryService;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('prevents entry when membership is expired', function () {
    $user = User::factory()->create(['membership_expires_at' => now()->subDay()]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $service = new EntryService();

    $this->expectException(HttpException::class);
    $service->entry($user, $tournament);
});

it('prevents entry when rank is insufficient', function () {
    $rank = createRank(1);
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
        'min_rank_level' => 2,
    ]);

    $service = new EntryService();

    $this->expectException(HttpException::class);
    $service->entry($user, $tournament);
});

it('prevents entry after deadline', function () {
    $rank = createRank(3);
    $user = User::factory()->create([
        'rank_id' => $rank->id,
        'membership_expires_at' => now()->addDay(),
    ]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => now()->subMinute(),
        'capacity' => null,
        'min_rank_level' => 0,
    ]);

    $service = new EntryService();

    $this->expectException(HttpException::class);
    $service->entry($user, $tournament);
});

it('prevents entry when capacity is reached', function () {
    $rank = createRank(3);
    $user = User::factory()->create([
        'rank_id' => $rank->id,
        'membership_expires_at' => now()->addDay(),
    ]);
    $tournament = Tournament::create([
        'title' => 'T',
        'description' => null,
        'event_date' => now()->toDateString(),
        'entry_deadline' => null,
        'capacity' => 1,
        'min_rank_level' => 0,
    ]);

    Entry::create([
        'user_id' => User::factory()->create()->id,
        'tournament_id' => $tournament->id,
        'status' => 'entry',
    ]);

    $service = new EntryService();

    $this->expectException(HttpException::class);
    $service->entry($user, $tournament);
});

it('returns existing entry when duplicated', function () {
    $rank = createRank(3);
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

    $existing = Entry::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => 'entry',
    ]);

    $service = new EntryService();
    $entry = $service->entry($user, $tournament);

    expect($entry->id)->toBe($existing->id);
});

it('creates entry when valid', function () {
    $rank = createRank(3);
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

    $service = new EntryService();
    $entry = $service->entry($user, $tournament);

    $this->assertDatabaseHas('entries', ['id' => $entry->id]);
});

it('cancels an entry', function () {
    $rank = createRank(3);
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

    Entry::create([
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => 'entry',
    ]);

    $service = new EntryService();
    $service->cancel($user, $tournament);

    $this->assertDatabaseHas('entries', [
        'user_id' => $user->id,
        'tournament_id' => $tournament->id,
        'status' => 'cancelled',
    ]);
});
