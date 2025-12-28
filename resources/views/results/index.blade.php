<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">成績一覧</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">大会</th>
                            <th class="text-left py-2">開催日</th>
                            <th class="text-left py-2">順位</th>
                            <th class="text-left py-2">スコア</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $r)
                            <tr class="border-b">
                                <td class="py-2">{{ $r->tournament->title ?? '-' }}</td>
                                <td class="py-2">{{ optional($r->tournament?->event_date)->format('Y-m-d') ?? '-' }}
                                </td>
                                <td class="py-2">{{ $r->placing ?? '-' }}</td>
                                <td class="py-2">{{ $r->score ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-4 text-gray-500" colspan="4">まだ成績がありません</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">{{ $results->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
