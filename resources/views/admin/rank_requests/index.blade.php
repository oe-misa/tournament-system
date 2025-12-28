<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">段位申請（履歴）</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">

                @if (session('status'))
                    <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
                @endif

                @if ($rankRequests->count() === 0)
                    <div class="text-gray-600">申請はありません。</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">申請者</th>
                                <th class="text-left py-2">申請段位</th>
                                <th class="text-left py-2">ステータス</th>
                                <th class="text-left py-2">担当者</th>
                                <th class="text-left py-2">日付(YYMMDD)</th>
                                <th class="text-left py-2">コメント</th>
                                <th class="text-left py-2">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rankRequests as $r)
                                <tr class="border-b align-top">
                                    <td class="py-2">
                                        <div class="font-semibold">{{ $r->user->name }}</div>
                                        <div class="text-gray-500">{{ $r->user->email }}</div>
                                    </td>

                                    <td class="py-2">
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

                                    <td class="py-2">
                                        @if ((int) $r->status === \App\Models\RankRequest::STATUS_PENDING)
                                            <span
                                                class="px-2 py-1 text-xs rounded-full bg-yellow-200 text-yellow-800">未処理</span>
                                        @elseif((int) $r->status === \App\Models\RankRequest::STATUS_APPROVED)
                                            <span
                                                class="px-2 py-1 text-xs rounded-full bg-green-200 text-green-800">承認</span>
                                        @elseif((int) $r->status === \App\Models\RankRequest::STATUS_REJECTED)
                                            <span
                                                class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700">却下</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-700">{{ $r->status }}</span>
                                        @endif
                                    </td>

                                    <td class="py-2">
                                        {{ $r->handledByName() }}
                                    </td>

                                    <td class="py-2 font-mono">
                                        {{ $r->displayDateYyMmDd() }}
                                    </td>

                                    <td class="py-2">
                                        @if ((int) $r->status === \App\Models\RankRequest::STATUS_PENDING)
                                            <form method="POST" class="space-y-2">
                                                @csrf

                                                <textarea name="admin_comment" rows="2" class="w-72 border-gray-300 rounded" placeholder="（任意）コメント">{{ old('admin_comment', '') }}</textarea>

                                                <div class="space-x-2">
                                                    <button type="submit"
                                                        formaction="{{ route('admin.rank_requests.approve', $r) }}"
                                                        class="px-3 py-1 rounded bg-green-600 text-white"
                                                        onclick="return confirm('承認してユーザー段位を更新します。よろしいですか？')">
                                                        承認
                                                    </button>

                                                    <button type="submit"
                                                        formaction="{{ route('admin.rank_requests.reject', $r) }}"
                                                        class="px-3 py-1 rounded bg-gray-700 text-white"
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

                                    <td class="py-2">
                                        @if ((int) $r->status === \App\Models\RankRequest::STATUS_PENDING)
                                            <span class="text-gray-500">操作は左で入力</span>
                                        @else
                                            <span class="text-gray-500">処理済み</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>{{ $rankRequests->links() }}</div>
                @endif

                <div class="pt-2">
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 underline">管理者ダッシュボードへ</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
