<nav x-data="{ open: false }" class="heian-nav">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4 py-3">
            <a href="{{ route('dashboard') }}" class="font-display text-xl font-bold text-[#9f3b30]">
                福岡かるた会員
            </a>

            <button
                class="inline-flex items-center justify-center rounded-md p-2 text-[#776b64] hover:bg-[#f4efe6] hover:text-[#34251f] focus:outline-none lg:hidden"
                @click="open = ! open"
                aria-label="メニュー"
            >
                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div class="hidden items-center gap-1 lg:flex">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    ホーム
                </x-nav-link>
                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                    会員情報
                </x-nav-link>
                <x-nav-link :href="route('tournaments.index')" :active="request()->routeIs('tournaments.*')">
                    大会登録
                </x-nav-link>
                <x-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')">
                    大会結果
                </x-nav-link>
                <x-nav-link :href="route('membership.create')" :active="request()->routeIs('membership.*')">
                    年間登録
                </x-nav-link>
                <x-nav-link :href="route('rank_requests.create')" :active="request()->routeIs('rank_requests.*')">
                    段位申請
                </x-nav-link>

                @if (auth()->user()?->is_admin)
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                        管理
                    </x-nav-link>
                @endif

                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button class="rounded-md px-3 py-1.5 text-sm font-medium text-[#776b64] hover:bg-[#f4efe6] hover:text-[#34251f]">
                        ログアウト
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden border-t border-[#e6ded2] bg-white lg:hidden">
        <div class="mx-auto max-w-6xl space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                ホーム
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                会員情報
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('tournaments.index')" :active="request()->routeIs('tournaments.*')">
                大会登録
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('results.index')" :active="request()->routeIs('results.*')">
                大会結果
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('membership.create')" :active="request()->routeIs('membership.*')">
                年間登録
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('rank_requests.create')" :active="request()->routeIs('rank_requests.*')">
                段位申請
            </x-responsive-nav-link>

            @if (auth()->user()?->is_admin)
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                    管理
                </x-responsive-nav-link>
            @endif

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    ログアウト
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
