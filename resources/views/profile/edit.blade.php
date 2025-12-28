<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">プロフィール</h2>
    </x-slot>

    @php
        $rankLevel = (int) ($user->rank?->level ?? 0);
    @endphp

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="p-3 bg-red-100 rounded">
                    <div class="font-semibold mb-2">入力内容にエラーがあります</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 現在情報 --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <div class="text-lg font-bold">現在の情報</div>

                <div class="text-sm text-gray-700">
                    段位：
                    <span class="font-semibold">{{ \App\Support\RankLabel::labelByLevel($rankLevel) }}</span>
                </div>

                <div class="text-sm text-gray-700">
                    年間登録期限：
                    <span class="font-semibold">
                        {{ $user->membership_expires_at ? $user->membership_expires_at->format('Y-m-d') : '未登録' }}
                    </span>
                </div>

                <div class="text-xs text-gray-500">
                    ※ 段位の変更は「段位申請」から行ってください。
                </div>

                <div>
                    <a href="{{ route('rank_requests.create') }}" class="text-blue-600 underline">段位申請へ</a>
                </div>
            </div>

            {{-- 基本情報更新 --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="text-lg font-bold">基本情報の変更</div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm font-medium">氏名</label>
                        <input name="name" class="mt-1 w-full border-gray-300 rounded"
                            value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">メールアドレス</label>
                        <input name="email" type="email" class="mt-1 w-full border-gray-300 rounded"
                            value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="border-t pt-4">
                        <div class="font-semibold">パスワード変更（任意）</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
                            <div>
                                <label class="block text-sm font-medium">新しいパスワード</label>
                                <input name="password" type="password" class="mt-1 w-full border-gray-300 rounded">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">新しいパスワード（確認）</label>
                                <input name="password_confirmation" type="password"
                                    class="mt-1 w-full border-gray-300 rounded">
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 mt-2">
                            ※ 変更しない場合は空欄のままでOKです
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded border">ダッシュボードへ</a>
                        <button class="px-4 py-2 rounded bg-indigo-600 text-white">更新する</button>
                    </div>
                </form>
            </div>

            {{-- アカウント削除 --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div class="text-lg font-bold text-red-700">アカウント削除</div>

                <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
                    @csrf
                    @method('DELETE')

                    <div class="text-sm text-gray-600">
                        アカウント削除には現在のパスワード入力が必要です。
                    </div>

                    <div>
                        <label class="block text-sm font-medium">現在のパスワード</label>
                        <input name="password" type="password" class="mt-1 w-full border-gray-300 rounded" required>
                    </div>

                    <button class="px-4 py-2 rounded bg-red-600 text-white"
                        onclick="return confirm('本当にアカウントを削除しますか？この操作は取り消せません。')">
                        削除する
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
