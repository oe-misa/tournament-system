<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">大会一覧</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <ul class="space-y-3">
                    @foreach ($tournaments as $t)
                        <li class="border-b pb-3">
                            <div class="font-bold">
                                <a class="text-blue-600 underline"
                                    href="{{ route('tournaments.show', $t) }}">{{ $t->title }}</a>
                            </div>
                            <div class="text-sm text-gray-600">開催日: {{ $t->event_date->format('Y-m-d') }}</div>
                            <div class="text-sm text-gray-600">
                                参加条件: {{ \App\Support\RankLabel::eligibleKyus($t->min_rank_level) }}
                            </div>

                        </li>
                    @endforeach
                </ul>

                <div class="mt-4">{{ $tournaments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
