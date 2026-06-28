<x-app-layout>
    <div class="hub-page-narrow space-y-6">
        <div>
            <h1 class="hub-title">会員編集</h1>
            <p class="hub-muted mt-1">{{ $user->name }} / {{ $user->email }}</p>
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
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium">氏名</label>
                    <input name="name" class="mt-1 w-full border-gray-300 rounded" value="{{ old('name', $user->name) }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium">メールアドレス</label>
                    <input name="email" type="email" class="mt-1 w-full border-gray-300 rounded" value="{{ old('email', $user->email) }}" required>
                </div>

                <div>
                    <label class="block text-sm font-medium">段位</label>
                    <select name="rank_id" class="mt-1 w-full border-gray-300 rounded">
                        <option value="">未設定</option>
                        @foreach ($ranks as $rank)
                            <option value="{{ $rank->id }}" @selected((string) old('rank_id', $user->rank_id) === (string) $rank->id)>
                                {{ \App\Support\RankLabel::labelByLevel((int) $rank->level) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">年間登録期限</label>
                    <input type="date" name="membership_expires_at" class="mt-1 w-full border-gray-300 rounded"
                        value="{{ old('membership_expires_at', optional($user->membership_expires_at)->format('Y-m-d')) }}">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_admin" value="1" class="rounded border-gray-300"
                        @checked(old('is_admin', $user->is_admin))>
                    <label class="text-sm font-medium">管理者</label>
                </div>

                <div class="flex justify-between gap-2">
                    <a href="{{ route('admin.users.index') }}" class="heian-btn-secondary">戻る</a>
                    <button class="heian-btn">更新</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
