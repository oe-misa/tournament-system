<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">大会 編集</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">

                @if (session('status'))
                    <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="p-3 bg-red-100 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">タイトル</label>
                        <input name="title" class="mt-1 w-full border-gray-300 rounded"
                            value="{{ old('title', $tournament->title) }}" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">説明</label>
                        <textarea name="description" class="mt-1 w-full border-gray-300 rounded" rows="4">{{ old('description', $tournament->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">開催日</label>
                            <input type="date" name="event_date" class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('event_date', $tournament->event_date->format('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">申込締切（任意）</label>
                            <input type="datetime-local" name="entry_deadline"
                                class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('entry_deadline', $tournament->entry_deadline ? $tournament->entry_deadline->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">定員（任意）</label>
                            <input type="number" name="capacity" class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('capacity', $tournament->capacity) }}" min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">参加条件（最低段位レベル）</label>
                            <select name="min_rank_level" class="mt-1 w-full border-gray-300 rounded">
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" @selected((int) old('min_rank_level', $tournament->min_rank_level) === $i)>
                                        {{ $i }}（{{ \App\Support\RankLabel::eligibleKyus($i) }}）
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament) }}">
                            @csrf
                            @method('DELETE')
                            <button class="px-4 py-2 rounded bg-red-600 text-white"
                                onclick="return confirm('削除しますか？')">削除</button>
                        </form>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.tournaments.index') }}" class="px-4 py-2 rounded border">戻る</a>
                            <button class="px-4 py-2 rounded bg-indigo-600 text-white">更新</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
