<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>福岡かるた会</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=kaisei-decol:400,500,700|zen-maru-gothic:400,500,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen site-shell">
        <header class="site-header">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex items-center justify-between">
                <a href="{{ route('member.mypage') }}" class="site-member-btn">
                    会員はこちら
                </a>

                <div class="flex items-center gap-3">
                    <div class="site-logo">
                        <span class="site-logo-green"></span>
                        <span class="site-logo-yellow"></span>
                        <span class="site-logo-red"></span>
                    </div>
                    <div class="leading-tight">
                        <div class="text-lg font-semibold text-[#222]">福岡県かるた協会</div>
                        <div class="text-[10px] tracking-[0.2em] text-[#6f6f6f]">FUKUOKA-KEN KARUTA ASSOCIATION</div>
                    </div>
                </div>

                <nav class="hidden sm:flex items-center gap-6 text-sm text-[#444] font-semibold">
                    <a href="#" class="hover:text-[#e34a6f]">ホーム</a>
                    <a href="#" class="hover:text-[#e34a6f]">お知らせ</a>
                </nav>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
                <section class="space-y-6">
                    <div class="site-hero">
                        <div class="site-hero-overlay"></div>
                        <div class="site-hero-caption">百人一首の情景</div>
                    </div>

                    <div class="text-sm text-[#666] border-b border-[#e2e2e2] pb-3">ホーム</div>

                    <section class="space-y-4">
                        <h1 class="text-2xl font-semibold text-[#333]">概要</h1>
                        <p class="text-sm leading-7 text-[#666]">
                            福岡県かるた協会は、（一社）全日本かるた協会九州支部において、
                            福岡県内における競技かるたの大会の企画、実施、
                            福岡県内の競技かるた普及活動（イベント、講習会など）、
                            所属する 10 登録会のご紹介、
                            各種情報発信（ホームページ等）等を行っております。
                        </p>
                        <ul class="text-sm leading-7 text-[#666] list-disc list-inside">
                            <li>福岡県内における競技かるたの大会の企画、実施</li>
                            <li>福岡県内の競技かるた普及活動（イベント、講習会など）</li>
                            <li>所属する10登録会のご紹介</li>
                            <li>各種情報発信（ホームページ等）</li>
                        </ul>
                    </section>

                    <section class="space-y-3">
                        <p class="text-sm text-[#666]">2024年4月1日より、</p>
                        <ul class="text-sm leading-7 text-[#666]">
                            <li>◇九州大学かるた会</li>
                            <li>◇なみき千早かるた会</li>
                            <li>◇福岡なのつるかるた会</li>
                            <li>◇福岡ゆうつづ会</li>
                            <li>◇ふくはなかるた会</li>
                            <li>◇字美かるた会</li>
                            <li>◇北九州かるた会</li>
                        </ul>
                    </section>
                </section>

                <aside class="space-y-6">
                    <div class="site-card p-4">
                        <div class="flex items-center gap-2">
                            <input class="site-input" type="text" placeholder="Enter Your search keyword ..." />
                            <button class="site-search-btn">Search</button>
                        </div>
                    </div>

                    <div class="site-card p-5">
                        <h2 class="text-base font-semibold text-[#333] border-b border-[#e2e2e2] pb-2">カテゴリー</h2>
                        <ul class="mt-3 space-y-2 text-sm text-[#555]">
                            <li class="site-cat-item">イベント</li>
                            <li class="site-cat-item">その他</li>
                            <li class="site-cat-item">メディア関係</li>
                            <li class="site-cat-item">大会</li>
                            <li class="site-cat-item">大会に関するお知らせ</li>
                            <li class="site-cat-item">大会案内</li>
                            <li class="site-cat-item">大会結果</li>
                            <li class="site-cat-item">昇段申請</li>
                            <li class="site-cat-item">未分類</li>
                            <li class="site-cat-item">講習会</li>
                            <li class="site-cat-item">速報</li>
                            <li class="site-cat-item">重要</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>
