<x-app-layout>
    <div class="hub-page space-y-6">
        <div>
            <h1 class="hub-title">段位申請管理</h1>
            <p class="hub-muted mt-1">未処理の承認・却下と、処理済み履歴を確認します。</p>
        </div>

                @if (session('status'))
                    <div class="hub-alert">{{ session('status') }}</div>
                @endif

                @if ($rankRequests->count() === 0)
                    <div class="heian-card p-6 hub-muted">申請はありません。</div>
                @else
                    <div class="heian-card overflow-hidden">
                        <div class="overflow-x-auto">
                    <table class="hub-table">
                        <thead>
                            <tr>
                                <th>申請者</th>
                                <th>申請段位</th>
                                <th>ステータス</th>
                                <th>担当者</th>
                                <th>日付</th>
                                <th>コメント</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rankRequests as $r)
                                <tr>
                                    <td>
                                        <div class="font-semibold">{{ $r->user->name }}</div>
                                        <div class="hub-muted">{{ $r->user->email }}</div>
                                    </td>

                                    <td>
                                        @php
                                            $rank = $r->requestedRank ?? $r->rank;
                                            $label = $rank
                                                ? \App\Support\RankLabel::labelByLevel((int) $rank->level)
                                                : (!is_null($r->requested_level ?? null)
                                                    ? \App\Support\RankLabel::labelByLevel((int) $r->requested_level)
                                                    : '（不明）');
                                        @endphp
                                        {{ $label }}
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

                                    <td>
                                        @if ($r->status === \App\Models\RankRequest::STATUS_PENDING)
                                            <form method="POST" class="space-y-2">
                                                @csrf

                                                <textarea name="admin_comment" rows="2" class="w-72" placeholder="（任意）コメント">{{ old('admin_comment', '') }}</textarea>

                                                <div class="flex gap-2">
                                                    <button type="submit"
                                                        formaction="{{ route('admin.rank_requests.approve', $r) }}"
                                                        class="heian-btn"
                                                        onclick="return confirm('承認してユーザー段位を更新します。よろしいですか？')">
                                                        承認
                                                    </button>

                                                    <button type="submit"
                                                        formaction="{{ route('admin.rank_requests.reject', $r) }}"
                                                        class="heian-btn-danger"
                                                        onclick="return confirm('却下します。よろしいですか？')">
                                                        却下
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="text-gray-700 whitespace-pre-wrap">
                                                {{ $r->admin_comment ?: '-' }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($r->status === \App\Models\RankRequest::STATUS_PENDING)
                                            <span class="hub-muted">操作は左で入力</span>
                                        @else
                                            <span class="hub-muted">処理済み</span>
                                        @endif
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
