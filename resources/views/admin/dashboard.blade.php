<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">管理者ダッシュボード</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4 border-l-4 border-indigo-600">
                <div class="text-lg font-bold text-indigo-700">管理者メニュー</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.rank_requests.index') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="font-semibold text-gray-800">段位申請管理</div>

                            @if (($pendingRankRequestsCount ?? 0) > 0)
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-red-600 text-white">
                                    未処理 {{ $pendingRankRequestsCount }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
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
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-orange-600 text-white">
                                    成績未入力 {{ $missingResultsCount }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-700">
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
