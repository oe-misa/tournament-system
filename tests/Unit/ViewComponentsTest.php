<?php

use App\View\Components\AppLayout;
use App\View\Components\GuestLayout;

it('renders layout components', function () {
    $appLayout = new AppLayout();
    $guestLayout = new GuestLayout();

    expect($appLayout->render()->name())->toBe('layouts.app');
    expect($guestLayout->render()->name())->toBe('layouts.guest');
});
