<x-app-layout>
    <div class="hub-page-narrow space-y-6">
        <div>
            <h1 class="hub-title">年間登録</h1>
            <p class="hub-muted mt-1">登録期間は毎年 4/1 から翌 3/31 までです。</p>
        </div>

        @if (session('status'))
            <div class="hub-alert">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="hub-alert-danger">{{ session('error') }}</div>
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

        <div class="heian-card p-6 space-y-5">
            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4">
                    <div class="hub-muted text-sm font-semibold">現在の期限</div>
                    <div class="mt-1 text-xl font-bold">
                        {{ $user->membership_expires_at ? $user->membership_expires_at->format('Y-m-d') : '未登録' }}
                    </div>
                </div>

                <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4">
                    <div class="hub-muted text-sm font-semibold">今回の登録期間</div>
                    <div class="mt-1 text-xl font-bold">
                        {{ $membershipPreview['start_date']->format('Y-m-d') }}
                        〜
                        {{ $membershipPreview['end_date']->format('Y-m-d') }}
                    </div>
                </div>
            </div>

            <div class="hub-muted text-sm">
                登録は1年度単位です。年間登録済みの会員は
                {{ $membershipPreview['renewal_starts_on']->format('n/j') }}
                から翌年度分を更新できます。
            </div>

            @if (!$membershipPreview['available'])
                <div class="hub-alert-danger">{{ $membershipPreview['reason'] }}</div>
            @endif

            <form method="POST" action="{{ route('membership.store') }}" class="space-y-4">
                @csrf

                <button class="heian-btn" @disabled(!$membershipPreview['available'])>
                    {{ $membershipPreview['is_next_fiscal_year'] ? '翌年度分を更新する' : '年間登録する' }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
