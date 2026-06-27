<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="font-display text-2xl font-bold text-[#9f3b30]">福岡かるた 会員ポータル</h1>
        <p class="hub-muted mt-1 text-sm">会員専用ページにログイン</p>
    </div>

    <div class="mb-4 flex items-center justify-between gap-3">
        <a href="{{ route('member.mypage') }}" class="heian-link text-sm">戻る</a>
        <x-auth-session-status :status="session('status')" />
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="メールアドレス" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="パスワード" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#dbcbb0] text-[#c1483c] shadow-sm focus:ring-[#c1483c]" name="remember">
                <span class="ms-2 text-sm text-gray-600">ログイン状態を保持</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    パスワードを忘れた場合
                </a>
            @endif

            <x-primary-button class="ms-3">
                ログイン
            </x-primary-button>
        </div>

    </form>
</x-guest-layout>
