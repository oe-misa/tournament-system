<?php

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

function makeLoginRequest(): LoginRequest
{
    $request = LoginRequest::create('/login', 'POST', [
        'email' => 'TEST@EXAMPLE.COM',
        'password' => 'password',
    ]);

    $request->setContainer(app());
    $request->setRedirector(app('redirect'));

    return $request;
}

it('returns throttle key', function () {
    $request = makeLoginRequest();
    $key = $request->throttleKey();

    expect($key)->toContain('test@example.com');
});

it('allows authenticate when not rate limited', function () {
    $request = makeLoginRequest();

    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(false);
    Auth::shouldReceive('attempt')->once()->andReturn(true);
    RateLimiter::shouldReceive('clear')->once();

    $request->authenticate();
});

it('throws validation exception when authentication fails', function () {
    $request = makeLoginRequest();

    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(false);
    Auth::shouldReceive('attempt')->once()->andReturn(false);
    RateLimiter::shouldReceive('hit')->once();

    $this->expectException(ValidationException::class);
    $request->authenticate();
});

it('throws validation exception when rate limited', function () {
    $request = makeLoginRequest();

    Event::fake([Lockout::class]);
    RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(true);
    RateLimiter::shouldReceive('availableIn')->once()->andReturn(60);

    try {
        $request->ensureIsNotRateLimited();
    } catch (ValidationException $e) {
        Event::assertDispatched(Lockout::class);
        expect($e->errors())->toHaveKey('email');
        return;
    }

    $this->fail('Expected ValidationException was not thrown.');
});
