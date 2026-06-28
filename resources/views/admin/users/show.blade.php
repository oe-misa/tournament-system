<x-app-layout>
    <div class="hub-page max-w-7xl space-y-6">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h1 class="hub-title">{{ $user->name }}</h1>
                <p class="hub-muted mt-1">{{ $user->email }}</p>
            </div>
            <a href="{{ route('admin.users.edit', $user) }}" class="heian-btn">編集</a>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="heian-card p-4">
                <div class="hub-muted text-xs font-semibold">段位</div>
                <div class="mt-1 font-semibold">{{ \App\Support\RankLabel::labelByLevel((int) ($user->rank?->level ?? 0)) }}</div>
            </div>
            <div class="heian-card p-4">
                <div class="hub-muted text-xs font-semibold">年間登録期限</div>
                <div class="mt-1 font-semibold">{{ $user->membership_expires_at ? $user->membership_expires_at->format('Y-m-d') : '未登録' }}</div>
            </div>
            <div class="heian-card p-4">
                <div class="hub-muted text-xs font-semibold">権限</div>
                <div class="mt-1 font-semibold">{{ $user->is_admin ? '管理者' : '会員' }}</div>
            </div>
            <div class="heian-card p-4">
                <div class="hub-muted text-xs font-semibold">更新日時</div>
                <div class="mt-1 font-semibold">{{ $user->updated_at?->format('Y-m-d H:i') ?? '-' }}</div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="heian-card p-5 space-y-3">
                <h2 class="font-semibold">最近のエントリー</h2>
                @if ($user->entries->isEmpty())
                    <div class="hub-muted">エントリーはありません。</div>
                @else
                    <ul class="space-y-2">
                        @foreach ($user->entries->take(10) as $entry)
                            <li class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-3">
                                <div class="font-semibold">{{ $entry->tournament?->title ?? '大会不明' }}</div>
                                <div class="hub-muted text-sm">
                                    {{ $entry->tournament?->event_date?->format('Y-m-d') ?? '-' }}
                                    / {{ $entry->status === \App\Models\Entry::STATUS_ENTRY ? 'エントリー済み' : 'キャンセル済み' }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="heian-card p-5 space-y-3">
                <h2 class="font-semibold">最近の段位申請</h2>
                @if ($user->rankRequests->isEmpty())
                    <div class="hub-muted">段位申請はありません。</div>
                @else
                    <ul class="space-y-2">
                        @foreach ($user->rankRequests->take(10) as $request)
                            <li class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-3">
                                <div class="font-semibold">
                                    {{ \App\Support\RankLabel::labelByLevel((int) ($request->requestedRank?->level ?? 0)) }}
                                </div>
                                <div class="hub-muted text-sm">
                                    {{ $request->statusLabel() }}
                                    / {{ $request->displayDateYyMmDd() }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
