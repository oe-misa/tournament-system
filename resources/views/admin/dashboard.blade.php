<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">管理者ダッシュボード</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="heian-card p-6 space-y-4 border-l-4 border-[#b08b3a]">
                <div class="text-lg font-bold text-[#6b4f2b]">管理者メニュー</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.rank_requests.index') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-gray-800">段位申請管理</div>

                            @if (($pendingRankRequestsCount ?? 0) > 0)
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-[#c1483c] text-white">
                                    未処理 {{ $pendingRankRequestsCount }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-[rgba(219,203,176,0.5)] text-[#6b645e]">
                                    未処理 0
                                </span>
                            @endif
                        </div>
                    </a>

                    <a href="{{ route('admin.tournaments.index') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-gray-800">大会管理</div>

                            @if (($missingResultsCount ?? 0) > 0)
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-[#b08b3a] text-white">
                                    成績未入力 {{ $missingResultsCount }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-[rgba(219,203,176,0.5)] text-[#6b645e]">
                                    成績未入力 0
                                </span>
                            @endif
                        </div>
                    </a>
                </div>

                <div class="text-xs text-gray-500">
                    ※「成績未入力」は「エントリー済み（status=entry）だが results が未作成」の件数です。
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
