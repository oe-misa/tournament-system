<x-app-layout>
    <div class="hub-page space-y-6">
        <div>
            <h1 class="hub-title">大会登録</h1>
            <p class="hub-muted mt-1">参加できる大会の一覧です。</p>
        </div>

        @if ($tournaments->count() === 0)
            <div class="heian-card p-6 hub-muted">現在表示できる大会はありません。</div>
        @else
            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($tournaments as $t)
                    <a href="{{ route('tournaments.show', $t) }}" class="hub-menu-card">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-lg font-semibold">{{ $t->title }}</h2>
                                <div class="hub-muted mt-2 text-sm">日程: {{ $t->event_date->format('Y-m-d') }}</div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="heian-pill">{{ $t->statusLabel() }}</span>
                                <span class="heian-pill">{{ \App\Support\RankLabel::eligibleKyus($t->min_rank_level) }}</span>
                            </div>
                        </div>
                        @if ($t->description)
                            <p class="hub-muted mt-3 text-sm">{{ \Illuminate\Support\Str::limit($t->description, 140) }}</p>
                        @endif
                    </a>
                @endforeach
            </div>

            <div>{{ $tournaments->links() }}</div>
        @endif
    </div>
</x-app-layout>
