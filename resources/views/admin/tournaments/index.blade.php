<x-app-layout>
    <div class="hub-page max-w-6xl space-y-6">
        <div class="flex items-end justify-between gap-3">
            <div>
                <h1 class="hub-title">大会管理</h1>
                <p class="hub-muted mt-1">大会情報の作成、編集、成績入力を行います。</p>
            </div>
            <a href="{{ route('admin.tournaments.create') }}" class="heian-btn">新規作成</a>
        </div>

        @if (session('status'))
            <div class="hub-alert">{{ session('status') }}</div>
        @endif

        <div class="heian-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="hub-table">
                    <thead>
                        <tr>
                            <th>大会</th>
                            <th>状態</th>
                            <th>開催日</th>
                            <th>参加条件</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tournaments as $t)
                            <tr>
                                <td class="font-semibold">{{ $t->title }}</td>
                                <td><span class="heian-pill">{{ $t->statusLabel() }}</span></td>
                                <td>{{ $t->event_date->format('Y-m-d') }}</td>
                                <td>{{ \App\Support\RankLabel::eligibleKyus($t->min_rank_level) }}</td>
                                <td class="space-x-3">
                                    <a class="heian-link"
                                        href="{{ route('admin.tournaments.edit', $t) }}">編集</a>
                                    <a class="heian-link"
                                        href="{{ route('admin.results.edit', $t) }}">成績入力</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $tournaments->links() }}</div>
    </div>
</x-app-layout>
