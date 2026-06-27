<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>福岡かるた 会員ポータル</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kaisei-decol:400,500,700|shippori-mincho:400,500,700|zen-maru-gothic:400,500,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <main class="flex min-h-screen items-center justify-center bg-[#fbf8f1] px-4 py-12">
        <div class="max-w-2xl space-y-8 text-center">
            <div class="space-y-4">
                <h1 class="font-display text-5xl font-bold leading-tight text-[#9f3b30] md:text-6xl">
                    福岡かるた<br>会員ポータル
                </h1>
                <p class="hub-muted text-lg">
                    大会の確認、年間登録の更新、段位申請、成績確認を会員ページから行えます。
                </p>
            </div>

            <div class="flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('member.mypage') }}" class="heian-btn">会員はこちら</a>
                <a href="https://fukuoka-karuta.com/" target="_blank" rel="noreferrer" class="heian-btn-secondary">公式サイト</a>
            </div>
        </div>
    </main>
</body>
</html>
