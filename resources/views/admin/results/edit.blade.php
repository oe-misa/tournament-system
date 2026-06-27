<x-app-layout>
    <div class="hub-page space-y-6">
        <div>
            <h1 class="hub-title">成績入力</h1>
            <p class="hub-muted mt-1">{{ $tournament->title }}</p>
        </div>

                @if (session('status'))
                    <div class="hub-alert">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="hub-alert-danger">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

        <div class="heian-card p-6 space-y-4">
                <form method="POST" action="{{ route('admin.results.update', $tournament) }}">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="hub-table">
                            <thead>
                                <tr>
                                    <th>参加者</th>
                                    <th>順位</th>
                                    <th>スコア</th>
                                    <th>メモ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entries as $e)
                                    @php $r = $results[$e->user_id] ?? null; @endphp
                                    <tr>
                                        <td>
                                            <div class="font-semibold">{{ $e->user->name }}</div>
                                            <div class="hub-muted">{{ $e->user->email }}</div>
                                        </td>
                                        <td>
                                            <input type="number" class="w-24"
                                                name="results[{{ $e->user_id }}][placing]"
                                                value="{{ old("results.$e->user_id.placing", $r->placing ?? '') }}">
                                        </td>
                                        <td>
                                            <input type="number" class="w-32"
                                                name="results[{{ $e->user_id }}][score]"
                                                value="{{ old("results.$e->user_id.score", $r->score ?? '') }}">
                                        </td>
                                        <td>
                                            <input class="w-full min-w-64"
                                                name="results[{{ $e->user_id }}][note]"
                                                value="{{ old("results.$e->user_id.note", $r->note ?? '') }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end gap-2 mt-4">
                        <a href="{{ route('admin.tournaments.index') }}" class="heian-btn-secondary">戻る</a>
                        <button class="heian-btn">保存</button>
                    </div>
                </form>
        </div>
    </div>
</x-app-layout>
