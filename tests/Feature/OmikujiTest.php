<?php

use App\Models\OmikujiDraw;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpKernel\Exception\HttpException;

it('allows a user to draw omikuji once per day', function () {
    $user = User::factory()->create();
    OmikujiDraw::query()->delete();

    $this->actingAs($user)
        ->post('/omikuji/draw')
        ->assertRedirect('/dashboard');

    $this->assertDatabaseCount('omikuji_draws', 1);
    $draw = OmikujiDraw::first();
    expect($draw->result)->toBeIn(['大吉', '吉', '中吉', '小吉', '凶']);

    $this->actingAs($user)
        ->post('/omikuji/draw')
        ->assertRedirect('/dashboard')
        ->assertSessionHas('status', '本日の御神籤は既に引いています');

    $this->assertDatabaseCount('omikuji_draws', 1);
});

it('allows a user to draw again on the next day', function () {
    $user = User::factory()->create();

    Carbon::setTestNow('2026-01-30 10:00:00');

    $this->actingAs($user)->post('/omikuji/draw');
    $this->assertDatabaseCount('omikuji_draws', 1);

    Carbon::setTestNow('2026-01-31 10:00:00');

    $this->actingAs($user)->post('/omikuji/draw');
    $this->assertDatabaseCount('omikuji_draws', 2);

    Carbon::setTestNow();
});

it('handles draw conflicts gracefully', function () {
    $user = User::factory()->create();

    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new HttpException(409, '本日の御神籤は既に引いています'));

    $this->actingAs($user)
        ->post('/omikuji/draw')
        ->assertRedirect('/dashboard')
        ->assertSessionHas('status', '本日の御神籤は既に引いています');
});

it('handles database constraint errors gracefully', function () {
    $user = User::factory()->create();

    DB::shouldReceive('transaction')
        ->once()
        ->andThrow(new QueryException('sqlite', 'insert', [], new Exception('duplicate')));

    $this->actingAs($user)
        ->post('/omikuji/draw')
        ->assertRedirect('/dashboard')
        ->assertSessionHas('status', '本日の御神籤は既に引いています');
});
