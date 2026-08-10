<x-app-layout>
    <div class="hub-page max-w-6xl space-y-6">
        <div>
            <h1 class="hub-title">管理</h1>
            <p class="hub-muted mt-1">大会、成績、段位申請、年間登録を運用します。</p>
        </div>

        <div class="heian-card p-6 space-y-4">
                <div class="grid grid-cols-2 gap-2 text-sm"><a href="{{ route('admin.memberships.index',['scope'=>'pending_payment']) }}">入金待ち {{ $membershipCounts['pending_payment'] }}</a><a href="{{ route('admin.memberships.index',['scope'=>'payment_confirmed']) }}">承認待ち {{ $membershipCounts['payment_confirmed'] }}</a><a class="text-red-700 font-semibold" href="{{ route('admin.memberships.index',['scope'=>'overdue_payment']) }}">申請7日超過 {{ $overduePaymentCount }}</a><a class="text-red-700 font-semibold" href="{{ route('admin.memberships.index',['scope'=>'overdue_approval']) }}">確認3日超過 {{ $overdueApprovalCount }}</a><span>承認済み {{ $membershipCounts['approved'] }}</span><span>却下 {{ $membershipCounts['rejected'] }}</span><span>有効会員 {{ $activeMemberCount }}</span><span>期限切れ {{ $expiredMemberCount }}</span></div>
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
                    <a href="{{ route('admin.memberships.index') }}" class="hub-menu-card">
                        <div class="font-semibold">年間登録管理</div>
                        <div class="hub-muted mt-2 text-sm">入金確認と承認を処理します。</div>
                    </a>
                </div>

                <div class="hub-muted text-xs">
                    「成績未入力」は「エントリー済みだが results が未作成」の件数です。下書き大会は会員一覧からは見えません。
                </div>
        </div>
    </div>
</x-app-layout>
