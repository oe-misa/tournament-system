<x-app-layout>
    <div class="hub-page max-w-7xl space-y-6">
        <div>
            <h1 class="hub-title">年間登録管理</h1>
            <p class="hub-muted mt-1">申請、入金確認、承認を順に処理します。</p>
        </div>
        @if (session('status')) <div class="hub-alert">{{ session('status') }}</div> @endif
        @if (session('error')) <div class="hub-alert-danger">{{ session('error') }}</div> @endif

        <div class="flex flex-wrap gap-2 text-sm">
            @foreach (['actionable' => '対応待ち', 'pending_payment' => '入金待ち', 'payment_confirmed' => '承認待ち', 'overdue_payment' => '申請7日超過', 'overdue_approval' => '確認3日超過', 'approved' => '承認済み', 'rejected' => '却下'] as $key => $label)
                <a class="heian-btn-secondary" href="{{ route('admin.memberships.index', ['scope' => $key]) }}">{{ $label }}</a>
            @endforeach
        </div>
        <div class="flex gap-2"><a class="heian-btn-secondary" href="{{ route('admin.memberships.report', request()->query()) }}">年度別レポート</a><a class="heian-btn-secondary" href="{{ route('admin.memberships.report.csv', request()->query()) }}">CSV出力</a><a class="heian-btn-secondary" target="_blank" href="{{ route('admin.memberships.report.print', request()->query()) }}">印刷用画面</a></div>
        <form method="GET" class="heian-card p-4 grid gap-2 md:grid-cols-4"><input name="q" value="{{ request('q') }}" placeholder="会員名・メール"><input name="year" value="{{ request('year') }}" placeholder="年度開始年"><select name="status"><option value="">全状態</option>@foreach(['pending_payment'=>'入金待ち','payment_confirmed'=>'承認待ち','approved'=>'承認済み','rejected'=>'却下'] as $key=>$label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach</select><input name="payment_reference" value="{{ request('payment_reference') }}" placeholder="振込名義・照合番号"><select name="payment_confirmed_by"><option value="">確認者</option>@foreach($admins as $admin)<option value="{{ $admin->id }}" @selected((string)request('payment_confirmed_by')===(string)$admin->id)>{{ $admin->name }}</option>@endforeach</select><select name="approved_by"><option value="">承認者</option>@foreach($admins as $admin)<option value="{{ $admin->id }}" @selected((string)request('approved_by')===(string)$admin->id)>{{ $admin->name }}</option>@endforeach</select><input name="comment" value="{{ request('comment') }}" placeholder="管理コメント"><button class="heian-btn">検索</button></form>

        <div class="heian-card overflow-x-auto p-5">
            <table class="hub-table">
                <thead><tr><th>会員</th><th>対象年度</th><th>状態</th><th>処理</th></tr></thead>
                <tbody>
                    @forelse ($memberships as $membership)
                        <tr @class(['bg-red-50' => ($membership->status === \App\Models\Membership::STATUS_PENDING_PAYMENT && $membership->created_at->lte(now()->subDays(7))) || ($membership->status === \App\Models\Membership::STATUS_PAYMENT_CONFIRMED && $membership->payment_confirmed_at?->lte(now()->subDays(3)))])>
                            <td><div class="font-semibold">{{ $membership->user->name }}</div><div class="hub-muted">{{ $membership->user->email }}</div></td>
                            <td>{{ $membership->start_date->format('Y-m-d') }} 〜 {{ $membership->end_date->format('Y-m-d') }}</td>
                            <td>{{ ['pending_payment' => '入金待ち', 'payment_confirmed' => '承認待ち', 'approved' => '承認済み', 'rejected' => '却下'][$membership->status] ?? $membership->status }} @if ($membership->status === \App\Models\Membership::STATUS_PENDING_PAYMENT && $membership->created_at->lte(now()->subDays(7)))<div class="text-xs text-red-700">申請から7日超過</div>@elseif ($membership->status === \App\Models\Membership::STATUS_PAYMENT_CONFIRMED && $membership->payment_confirmed_at?->lte(now()->subDays(3)))<div class="text-xs text-red-700">確認から3日超過</div>@endif</td>
                            <td class="space-y-2">
                                @if ($membership->status === \App\Models\Membership::STATUS_PENDING_PAYMENT)
                                    <form method="POST" action="{{ route('admin.memberships.confirm_payment', $membership) }}" class="space-y-1">@csrf
                                        <input name="payment_reference" class="w-full" placeholder="振込名義・照合番号">
                                        <input type="date" name="payment_confirmed_on" value="{{ today()->format('Y-m-d') }}" class="w-full">
                                        <input name="admin_comment" class="w-full" placeholder="管理メモ（任意）">
                                        <button class="heian-btn">入金を確認</button>
                                    </form>
                                @endif
                                @if ($membership->status === \App\Models\Membership::STATUS_PAYMENT_CONFIRMED)
                                    <form method="POST" action="{{ route('admin.memberships.approve', $membership) }}">@csrf <input name="admin_comment" class="w-full" placeholder="承認コメント（任意）"> <button class="heian-btn">承認して期限を更新</button></form>
                                @endif
                                @if (in_array($membership->status, [\App\Models\Membership::STATUS_PENDING_PAYMENT, \App\Models\Membership::STATUS_PAYMENT_CONFIRMED], true))
                                    <form method="POST" action="{{ route('admin.memberships.reject', $membership) }}">@csrf <input name="admin_comment" class="w-full" placeholder="却下理由"> <button class="heian-btn-danger">却下</button></form>
                                @endif
                                @if ($membership->admin_comment)<div class="hub-muted text-xs">{{ $membership->admin_comment }}</div>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="hub-muted">該当する申請はありません。</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">{{ $memberships->links() }}</div>
        </div>
    </div>
</x-app-layout>
