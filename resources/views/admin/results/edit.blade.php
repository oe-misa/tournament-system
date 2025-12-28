<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">成績入力：{{ $tournament->title }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                <form method="POST" action="{{ route('admin.results.update', $tournament) }}">
                    @csrf

                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">参加者</th>
                                <th class="text-left py-2">順位</th>
                                <th class="text-left py-2">スコア</th>
                                <th class="text-left py-2">メモ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entries as $e)
                                @php $r = $results[$e->user_id] ?? null; @endphp
                                <tr class="border-b">
                                    <td class="py-2">
                                        {{ $e->user->name }}<br>
                                        <span class="text-gray-500">{{ $e->user->email }}</span>
                                    </td>
                                    <td class="py-2">
                                        <input type="number" class="w-24 border-gray-300 rounded"
                                            name="results[{{ $e->user_id }}][placing]"
                                            value="{{ old("results.$e->user_id.placing", $r->placing ?? '') }}">
                                    </td>
                                    <td class="py-2">
                                        <input type="number" class="w-32 border-gray-300 rounded"
                                            name="results[{{ $e->user_id }}][score]"
                                            value="{{ old("results.$e->user_id.score", $r->score ?? '') }}">
                                    </td>
                                    <td class="py-2">
                                        <input class="w-full border-gray-300 rounded"
                                            name="results[{{ $e->user_id }}][note]"
                                            value="{{ old("results.$e->user_id.note", $r->note ?? '') }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="flex justify-end gap-2 mt-4">
                        <a href="{{ route('admin.tournaments.index') }}" class="px-4 py-2 rounded border">戻る</a>
                        <button class="px-4 py-2 rounded bg-indigo-600 text-white">保存</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
