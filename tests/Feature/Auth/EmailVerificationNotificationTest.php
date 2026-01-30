<?php

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;

it('sends verification notification for unverified users', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertRedirect()
        ->assertSessionHas('status', 'verification-link-sent');

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('redirects verified users to dashboard when requesting verification', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertRedirect(route('dashboard', absolute: false));
});
