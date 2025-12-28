<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">段位申請</h2>
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
                    現在の段位レベル: <span class="font-bold">{{ $user->rank?->level ?? 0 }}</span>
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

                @if ($ranks->isEmpty())
                    <div class="text-gray-600">申請できる上位段位がありません。</div>
                @else
                    <form method="POST" action="{{ route('rank_requests.store') }}" class="space-y-3">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium">申請する段位</label>
                            <select name="rank_id" class="mt-1 block w-full border-gray-300 rounded">
                                @foreach ($ranks as $rank)
                                    <option value="{{ $rank->id }}">
                                        {{ $rank->kyu }}級 / {{ $rank->dan ? $rank->dan . '段' : '無段' }}（level
                                        {{ $rank->level }}）
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">コメント（任意）</label>
                            <textarea name="comment" class="mt-1 block w-full border-gray-300 rounded" rows="4">{{ old('comment') }}</textarea>
                        </div>

                        <x-primary-button>申請する</x-primary-button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
