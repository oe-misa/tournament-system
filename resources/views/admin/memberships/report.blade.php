<x-app-layout>
    <div class="hub-page max-w-7xl space-y-6">
        <div><h1 class="hub-title">{{ $year }}年度 年間登録レポート</h1><p class="hub-muted mt-1">検索条件に一致する年間登録の一覧です。</p></div>
        <form method="GET" class="heian-card p-4 flex flex-wrap gap-2"><input name="year" value="{{ request('year', $year) }}" placeholder="年度開始年"><input name="q" value="{{ request('q') }}" placeholder="会員名・メール"><select name="status"><option value="">全状態</option>@foreach(['pending_payment'=>'入金待ち','payment_confirmed'=>'承認待ち','approved'=>'承認済み','rejected'=>'却下'] as $key=>$label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select><button class="heian-btn">絞り込み</button><a class="heian-btn-secondary" href="{{ route('admin.memberships.report.csv', request()->query()) }}">CSV出力</a><a class="heian-btn-secondary" target="_blank" href="{{ route('admin.memberships.report.print', request()->query()) }}">印刷用画面</a></form>
        @include('admin.memberships.table', ['memberships' => $memberships])
    </div>
</x-app-layout>
