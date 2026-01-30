<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>会員ページ</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kaisei-decol:400,500,700|zen-maru-gothic:400,500,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen heian-shell">
        <header class="heian-header">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
                <a href="{{ route('site.landing') }}" class="heian-link text-sm">トップへ戻る</a>
                <div class="text-sm tracking-[0.2em] text-[#6b5a3d]">会員ページ</div>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="heian-card p-8 sm:p-10 space-y-6">
                <div class="space-y-3">
                    <h1 class="text-2xl sm:text-3xl">会員専用マイページ</h1>
                    <p class="text-sm sm:text-base heian-text-muted">
                        段位申請や大会エントリーなどの機能はログイン後に利用できます。
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}" class="heian-btn text-center">ログイン</a>
                    <a href="{{ route('register') }}" class="heian-btn-secondary text-center">新規登録</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
