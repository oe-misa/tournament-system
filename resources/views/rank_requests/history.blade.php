<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">段位申請 履歴</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                @if ($rankRequests->count() === 0)
                    <div class="text-gray-600">まだ段位申請の履歴はありません。</div>
                @else
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">申請段位</th>
                                <th class="text-left py-2">ステータス</th>
                                <th class="text-left py-2">担当者</th>
                                <th class="text-left py-2">日付(YYMMDD)</th>
                                <th class="text-left py-2">コメント</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rankRequests as $r)
                                <tr class="border-b align-top">
                                    <td class="py-2">
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

                                    <td class="py-2 whitespace-pre-wrap">
                                        {{ $r->admin_comment ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>{{ $rankRequests->links() }}</div>
                @endif

                <div class="pt-2 flex gap-4">
                    <a href="{{ route('rank_requests.create') }}" class="text-blue-600 underline">段位申請へ</a>
                    <a href="{{ route('dashboard') }}" class="text-blue-600 underline">ダッシュボードへ</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
