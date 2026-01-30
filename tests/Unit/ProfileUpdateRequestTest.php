<?php

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;

it('returns profile update rules with unique email ignoring current user', function () {
    $user = User::factory()->create();

    $request = ProfileUpdateRequest::create('/profile', 'PATCH', [
        'name' => 'Test User',
        'email' => $user->email,
    ]);

    $request->setUserResolver(fn () => $user);
    $rules = $request->rules();

    expect($rules)->toHaveKey('email');
});
