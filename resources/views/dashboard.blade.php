<x-app-layout>
    <div class="hub-page space-y-6">
        <div>
            <h1 class="hub-title">こんにちは、{{ $user->name ?: '会員' }} 様</h1>
            <p class="hub-muted mt-1">
                段位: {{ \App\Support\RankLabel::labelByLevel((int) ($user->rank?->level ?? 0)) }}
                <span class="mx-2">/</span>
                年間登録: {{ $user->membership_expires_at ? $user->membership_expires_at->format('Y-m-d') . 'まで' : '未登録' }}
            </p>
        </div>

        @if (session('status'))
            <div class="hub-alert">{{ session('status') }}</div>
        @endif

        @if (!$user->membership_expires_at || $user->membership_expires_at->isPast())
            <div class="hub-alert-danger">
                <div class="font-semibold">年間登録が未登録、または期限切れです。</div>
                <div class="mt-1 text-sm">大会へのエントリーには有効な年間登録が必要です。</div>
                <a href="{{ route('membership.create') }}" class="mt-3 inline-block text-sm font-semibold text-[#9f3b30]">年間登録ページへ</a>
            </div>
        @endif

        <div class="heian-card p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="font-semibold">本日の御神籤</div>
                    @if ($todayOmikuji)
                        <div class="hub-muted mt-1 text-sm">結果: <span class="font-bold text-[#9f3b30]">{{ $todayOmikuji->result }}</span></div>
                    @else
                        <div class="hub-muted mt-1 text-sm">1日1回だけ引けます。</div>
                    @endif
                </div>

                @if ($todayOmikuji)
                    <button class="heian-btn-secondary" disabled>本日は引きました</button>
                @else
                    <form method="POST" action="{{ route('omikuji.draw') }}">
                        @csrf
                        <button class="heian-btn">本日の御神籤を引く</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('profile.edit') }}" class="hub-menu-card">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold">会員情報</div>
                        <div class="hub-muted mt-2 text-sm">プロフィールと登録状況</div>
                    </div>
                    <span class="heian-pill">Profile</span>
                </div>
            </a>

            <a href="{{ route('tournaments.index') }}" class="hub-menu-card">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold">大会登録</div>
                        <div class="hub-muted mt-2 text-sm">大会一覧とエントリー</div>
                    </div>
                    <span class="heian-pill">Entry</span>
                </div>
            </a>

            <a href="{{ route('results.index') }}" class="hub-menu-card">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold">大会結果</div>
                        <div class="hub-muted mt-2 text-sm">過去成績の確認</div>
                    </div>
                    <span class="heian-pill">Result</span>
                </div>
            </a>

            <a href="{{ route('membership.create') }}" class="hub-menu-card">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="font-semibold">年間登録</div>
                        <div class="hub-muted mt-2 text-sm">登録期限の更新</div>
                    </div>
                    <span class="heian-pill">{{ $user->membership_expires_at && !$user->membership_expires_at->isPast() ? '有効' : '要更新' }}</span>
                </div>
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <a href="{{ route('rank_requests.create') }}" class="hub-menu-card">
                <div class="font-semibold">段位申請</div>
                <div class="hub-muted mt-2 text-sm">現在の段位以上への申請を行います。</div>
            </a>
            <a href="{{ route('rank_requests.history') }}" class="hub-menu-card">
                <div class="font-semibold">段位申請履歴</div>
                <div class="hub-muted mt-2 text-sm">申請状況、担当者、管理者コメントを確認します。</div>
            </a>
        </div>

        @if ($user->is_admin)
            <div class="heian-card p-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-bold">管理</h2>
                        <p class="hub-muted mt-1 text-sm">大会、成績、段位申請の運用メニュー</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="heian-btn-secondary">管理画面へ</a>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <a href="{{ route('admin.rank_requests.index') }}" class="hub-menu-card">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-semibold">段位申請管理</span>
                            @if (($pendingRankRequestsCount ?? 0) > 0)
                                <span class="heian-pill">未処理 {{ $pendingRankRequestsCount }}</span>
                            @endif
                        </div>
                    </a>
                    <a href="{{ route('admin.tournaments.index') }}" class="hub-menu-card">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-semibold">大会管理</span>
                            @if (($missingResultsCount ?? 0) > 0)
                                <span class="heian-pill">成績未入力 {{ $missingResultsCount }}</span>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
