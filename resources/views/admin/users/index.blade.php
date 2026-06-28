<x-app-layout>
    <div class="hub-page max-w-7xl space-y-6">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h1 class="hub-title">会員管理</h1>
                <p class="hub-muted mt-1">会員の段位、年間登録期限、管理者権限を確認・更新します。</p>
            </div>
        </div>

        <form method="GET" class="heian-card p-4 flex gap-3">
            <input name="q" value="{{ $search }}" class="w-full max-w-xl border-gray-300 rounded" placeholder="名前またはメールアドレスで検索">
            <button class="heian-btn">検索</button>
        </form>

        <div class="heian-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="hub-table">
                    <thead>
                        <tr>
                            <th>会員</th>
                            <th>段位</th>
                            <th>年間登録</th>
                            <th>権限</th>
                            <th>実績</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $user->name }}</div>
                                    <div class="hub-muted">{{ $user->email }}</div>
                                </td>
                                <td>{{ \App\Support\RankLabel::labelByLevel((int) ($user->rank?->level ?? 0)) }}</td>
                                <td>
                                    @if ($user->membership_expires_at)
                                        {{ $user->membership_expires_at->format('Y-m-d') }}
                                    @else
                                        <span class="hub-muted">未登録</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($user->is_admin)
                                        <span class="heian-pill">管理者</span>
                                    @else
                                        <span class="heian-pill">会員</span>
                                    @endif
                                </td>
                                <td class="space-x-2">
                                    <span class="heian-pill">大会 {{ $user->entries_count }}</span>
                                    <span class="heian-pill">成績 {{ $user->results_count }}</span>
                                    <span class="heian-pill">申請 {{ $user->rank_requests_count }}</span>
                                </td>
                                <td class="space-x-3">
                                    <a class="heian-link" href="{{ route('admin.users.show', $user) }}">詳細</a>
                                    <a class="heian-link" href="{{ route('admin.users.edit', $user) }}">編集</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $users->links() }}</div>
    </div>
</x-app-layout>
