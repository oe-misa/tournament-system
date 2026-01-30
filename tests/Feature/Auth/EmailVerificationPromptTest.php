<?php

use App\Models\User;

it('shows verification prompt for unverified users', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertOk();
});

it('redirects verified users from verification prompt', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/verify-email')
        ->assertRedirect(route('dashboard', absolute: false));
});
