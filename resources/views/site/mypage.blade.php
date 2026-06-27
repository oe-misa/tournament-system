<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>会員ページ</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kaisei-decol:400,500,700|shippori-mincho:400,500,700|zen-maru-gothic:400,500,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <main class="flex min-h-screen items-center justify-center bg-[#fbf8f1] px-4 py-12">
        <div class="w-full max-w-md">
            <div class="heian-card p-8 text-center">
                <a href="{{ route('site.landing') }}" class="heian-link text-sm">トップへ戻る</a>
                <h1 class="font-display mt-6 text-3xl font-bold text-[#9f3b30]">福岡かるた 会員ポータル</h1>
                <p class="hub-muted mt-3 text-sm">会員専用ページにログイン、または新規登録してください。</p>

                <div class="mt-8 grid gap-3">
                    <a href="{{ route('login') }}" class="heian-btn">ログイン</a>
                    <a href="{{ route('register') }}" class="heian-btn-secondary">新規登録</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
