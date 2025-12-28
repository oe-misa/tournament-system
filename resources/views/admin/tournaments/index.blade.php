<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">大会管理</h2>
            <a href="{{ route('admin.tournaments.create') }}" class="px-4 py-2 rounded bg-indigo-600 text-white">新規作成</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-3">
                @if (session('status'))
                    <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
                @endif

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">大会</th>
                            <th class="text-left py-2">開催日</th>
                            <th class="text-left py-2">参加条件</th>
                            <th class="text-left py-2">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tournaments as $t)
                            <tr class="border-b">
                                <td class="py-2 font-semibold">{{ $t->title }}</td>
                                <td class="py-2">{{ $t->event_date->format('Y-m-d') }}</td>
                                <td class="py-2">{{ \App\Support\RankLabel::eligibleKyus($t->min_rank_level) }}</td>
                                <td class="py-2 space-x-3">
                                    <a class="text-blue-600 underline"
                                        href="{{ route('admin.tournaments.edit', $t) }}">編集</a>
                                    <a class="text-blue-600 underline"
                                        href="{{ route('admin.results.edit', $t) }}">成績入力</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div>{{ $tournaments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
