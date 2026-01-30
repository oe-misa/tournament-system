<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('profile update can change password via profile form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch('/profile', [
            'name' => 'New Name',
            'email' => 'new@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertRedirect('/profile');

    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});
