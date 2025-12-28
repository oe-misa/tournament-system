<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">大会詳細</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                @if (session('status'))
                    <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-3 bg-red-100 rounded">{{ session('error') }}</div>
                @endif

                <div class="text-2xl font-bold">{{ $tournament->title }}</div>
                <div class="text-gray-700 whitespace-pre-wrap">{{ $tournament->description }}</div>

                <div class="text-sm text-gray-600">開催日: {{ $tournament->event_date->format('Y-m-d') }}</div>
                <div class="text-sm text-gray-600">
                    参加条件: {{ \App\Support\RankLabel::eligibleKyus($t->min_rank_level) }}
                </div>


                <form method="POST" action="{{ route('entries.store', $tournament) }}">
                    @csrf
                    <x-primary-button>この大会にエントリー</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
