<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">大会 新規作成</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">

                @if ($errors->any())
                    <div class="p-3 bg-red-100 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.tournaments.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">タイトル</label>
                        <input name="title" class="mt-1 w-full border-gray-300 rounded" value="{{ old('title') }}"
                            required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">説明</label>
                        <textarea name="description" class="mt-1 w-full border-gray-300 rounded" rows="4">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">開催日</label>
                            <input type="date" name="event_date" class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('event_date') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">申込締切（任意）</label>
                            <input type="datetime-local" name="entry_deadline"
                                class="mt-1 w-full border-gray-300 rounded" value="{{ old('entry_deadline') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">定員（任意）</label>
                            <input type="number" name="capacity" class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('capacity') }}" min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">参加条件（最低段位レベル）</label>
                            <select name="min_rank_level" class="mt-1 w-full border-gray-300 rounded">
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" @selected((int) old('min_rank_level', 0) === $i)>
                                        {{ $i }}（{{ \App\Support\RankLabel::eligibleKyus($i) }}）
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.tournaments.index') }}" class="px-4 py-2 rounded border">戻る</a>
                        <button class="px-4 py-2 rounded bg-indigo-600 text-white">作成</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
