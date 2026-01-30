<?php

use App\Models\User;

it('blocks non-admin users from admin routes', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertStatus(403);
});

it('allows admin users to access admin routes', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});
