<x-app-layout>
    <div class="hub-page max-w-6xl space-y-6">
        <div>
            <h1 class="hub-title">管理</h1>
            <p class="hub-muted mt-1">大会、成績、段位申請を運用します。</p>
        </div>

        <div class="heian-card p-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <a href="{{ route('admin.rank_requests.index') }}" class="hub-menu-card">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">段位申請管理</div>

                            @if (($pendingRankRequestsCount ?? 0) > 0)
                                <span class="heian-pill">未処理 {{ $pendingRankRequestsCount }}</span>
                            @else
                                <span class="heian-pill">未処理 0</span>
                            @endif
                        </div>
                    </a>

                    <a href="{{ route('admin.tournaments.index') }}" class="hub-menu-card">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">大会管理</div>
                            <div class="flex gap-2">
                                <span class="heian-pill">成績未入力 {{ $missingResultsCount ?? 0 }}</span>
                                <span class="heian-pill">下書き {{ $draftTournamentCount ?? 0 }}</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="hub-menu-card">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold">会員管理</div>
                            <span class="heian-pill">会員 {{ $memberCount ?? 0 }}</span>
                        </div>
                    </a>
                </div>

                <div class="hub-muted text-xs">
                    「成績未入力」は「エントリー済みだが results が未作成」の件数です。下書き大会は会員一覧からは見えません。
                </div>
        </div>
    </div>
</x-app-layout>
