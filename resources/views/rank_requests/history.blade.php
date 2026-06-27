<x-app-layout>
    <div class="hub-page space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="hub-title">段位申請履歴</h1>
                <p class="hub-muted mt-1">申請状況、担当者、管理者コメントを確認できます。</p>
            </div>
            <a href="{{ route('rank_requests.create') }}" class="heian-btn-secondary">新しく申請する</a>
        </div>

        @if ($rankRequests->count() === 0)
            <div class="heian-card p-6 hub-muted">まだ段位申請の履歴はありません。</div>
        @else
            <div class="heian-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="hub-table">
                        <thead>
                            <tr>
                                <th>申請段位</th>
                                <th>ステータス</th>
                                <th>担当者</th>
                                <th>日付</th>
                                <th>コメント</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rankRequests as $r)
                                <tr>
                                    <td>
                                        @php
                                            $rank = $r->requestedRank ?? $r->rank;
                                            $label = $rank
                                                ? \App\Support\RankLabel::labelByLevel((int) $rank->level)
                                                : (!is_null($r->requested_level ?? null)
                                                    ? \App\Support\RankLabel::labelByLevel((int) $r->requested_level)
                                                    : '（不明）');
                                        @endphp
                                        <div class="font-semibold">{{ $label }}</div>
                                    </td>

                                    <td>
                                        @if ($r->status === \App\Models\RankRequest::STATUS_PENDING)
                                            <span class="heian-pill">未処理</span>
                                        @elseif($r->status === \App\Models\RankRequest::STATUS_APPROVED)
                                            <span class="heian-pill">承認</span>
                                        @elseif($r->status === \App\Models\RankRequest::STATUS_REJECTED)
                                            <span class="heian-pill">却下</span>
                                        @else
                                            <span class="heian-pill">{{ $r->status }}</span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $r->handledByName() }}
                                    </td>

                                    <td class="font-mono">
                                        {{ $r->displayDateYyMmDd() }}
                                    </td>

                                    <td class="whitespace-pre-wrap">
                                        {{ $r->admin_comment ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $rankRequests->links() }}</div>
        @endif
    </div>
</x-app-layout>
