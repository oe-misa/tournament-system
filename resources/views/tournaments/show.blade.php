<x-app-layout>
    <div class="hub-page-narrow space-y-6">
        @if (session('status'))
            <div class="hub-alert">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="hub-alert-danger">{{ session('error') }}</div>
        @endif

        <div class="heian-card p-6 space-y-5">
            <div>
                <a href="{{ route('tournaments.index') }}" class="heian-link text-sm">大会一覧へ戻る</a>
                <h1 class="hub-title mt-3">{{ $tournament->title }}</h1>
            </div>

            @if ($tournament->description)
                <p class="hub-muted whitespace-pre-wrap">{{ $tournament->description }}</p>
            @endif

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4">
                    <div class="hub-muted text-xs font-semibold">日程</div>
                    <div class="mt-1 font-semibold">{{ $tournament->event_date->format('Y-m-d') }}</div>
                </div>
                <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4">
                    <div class="hub-muted text-xs font-semibold">参加条件</div>
                    <div class="mt-1 font-semibold">{{ \App\Support\RankLabel::eligibleKyus($tournament->min_rank_level) }}</div>
                </div>
                @if ($tournament->entry_deadline)
                    <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4">
                        <div class="hub-muted text-xs font-semibold">締切</div>
                        <div class="mt-1 font-semibold">{{ $tournament->entry_deadline->format('Y-m-d H:i') }}</div>
                    </div>
                @endif
                @if (!is_null($tournament->capacity))
                    <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4">
                        <div class="hub-muted text-xs font-semibold">定員</div>
                        <div class="mt-1 font-semibold">{{ $tournament->capacity }}名</div>
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('entries.store', $tournament) }}">
                @csrf
                <button class="heian-btn w-full sm:w-auto">参加登録</button>
            </form>
        </div>
    </div>
</x-app-layout>
