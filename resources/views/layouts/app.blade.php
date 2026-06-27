<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kaisei-decol:400,500,700|shippori-mincho:400,500,700|zen-maru-gothic:400,500,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="min-h-screen heian-shell">
        @include('layouts.navigation')

        @isset($header)
            <header class="heian-header">
                <div class="max-w-6xl mx-auto pt-8 px-4 sm:px-6 lg:px-8 flex items-center justify-between">
                    <div>
                        {{ $header }}
                    </div>
                </div>
            </header>
        @endisset

        <main>
            {{ $slot }}
        </main>
    </div>
</body>

</html>
