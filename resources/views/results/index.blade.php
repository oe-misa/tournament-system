<x-app-layout>
    <div class="hub-page space-y-6">
        <div>
            <h1 class="hub-title">大会結果</h1>
            <p class="hub-muted mt-1">これまでの成績一覧です。</p>
        </div>

        <div class="heian-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="hub-table">
                    <thead>
                        <tr>
                            <th>大会</th>
                            <th>開催日</th>
                            <th>順位</th>
                            <th>スコア</th>
                            <th>備考</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $r)
                            <tr>
                                <td class="font-semibold">{{ $r->tournament->title ?? '-' }}</td>
                                <td>{{ optional($r->tournament?->event_date)->format('Y-m-d') ?? '-' }}</td>
                                <td>{{ $r->placing ?? '-' }}</td>
                                <td>{{ $r->score ?? '-' }}</td>
                                <td class="whitespace-pre-wrap">{{ $r->note ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="hub-muted py-6" colspan="5">まだ成績がありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $results->links() }}</div>
    </div>
</x-app-layout>
