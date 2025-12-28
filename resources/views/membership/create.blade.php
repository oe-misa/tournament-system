<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">年間登録更新</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-4">
                @if (session('status'))
                    <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="p-3 bg-red-100 rounded">{{ session('error') }}</div>
                @endif

                <div class="text-sm text-gray-700">
                    現在の期限:
                    <span class="font-bold">
                        {{ $user->membership_expires_at ? $user->membership_expires_at->format('Y-m-d') : '未登録' }}
                    </span>
                </div>

                @if ($errors->any())
                    <div class="p-3 bg-red-100 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('membership.store') }}" class="space-y-3">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">更新年数</label>
                        <select name="years" class="mt-1 block w-full border-gray-300 rounded">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }}年</option>
                            @endfor
                        </select>
                    </div>

                    <x-primary-button>更新する</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
