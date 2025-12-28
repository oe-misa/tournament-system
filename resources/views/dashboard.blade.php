<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ダッシュボード
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 会員メニュー --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="text-lg font-bold">メニュー</div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('tournaments.index') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="font-semibold">大会一覧</div>
                        <div class="text-sm text-gray-600 mt-1">大会の確認・エントリー</div>
                    </a>

                    <a href="{{ route('results.index') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="font-semibold">成績一覧</div>
                        <div class="text-sm text-gray-600 mt-1">過去成績の確認</div>
                    </a>

                    <a href="{{ route('rank_requests.create') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="font-semibold">段位申請</div>
                        <div class="text-sm text-gray-600 mt-1">昇段申請の提出</div>
                    </a>

                    <a href="{{ route('membership.create') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="font-semibold">年間登録更新</div>
                        <div class="text-sm text-gray-600 mt-1">登録期限の更新</div>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="block p-4 rounded border hover:bg-gray-50">
                        <div class="font-semibold">プロフィール</div>
                        <div class="text-sm text-gray-600 mt-1">ユーザー情報の編集</div>
                    </a>
                </div>
            </div>

            {{-- 管理者メニュー（管理者だけ表示） --}}
            @if ($user->is_admin)
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4 border-l-4 border-indigo-600">
                    <div class="text-lg font-bold text-indigo-700">管理者メニュー</div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('admin.tournaments.index') }}"
                            class="block p-4 rounded border hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold">大会管理</span>

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

                            <div class="text-sm text-gray-600 mt-2">大会作成・編集・削除、成績入力</div>
                        </a>

                        <a href="{{ route('admin.rank_requests.index') }}"
                            class="block p-4 rounded border hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold">段位申請管理</span>

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

                            <div class="text-sm text-gray-600 mt-2">申請の承認・却下</div>
                        </a>

                        <a href="{{ route('admin.dashboard') }}"
                            class="block p-4 rounded border hover:bg-gray-50 sm:col-span-2">
                            <div class="font-semibold">管理者ダッシュボード</div>
                            <div class="text-sm text-gray-600 mt-1">管理者状況サマリ（バッジ確認）</div>
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
