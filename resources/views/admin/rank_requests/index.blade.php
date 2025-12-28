<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">段位申請（承認/却下）</h2>
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

                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">状態</th>
                            <th class="text-left py-2">申請者</th>
                            <th class="text-left py-2">申請段位</th>
                            <th class="text-left py-2">申請日時</th>
                            <th class="text-left py-2">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $r)
                            <tr class="border-b align-top">
                                <td class="py-2 font-semibold">{{ $r->status }}</td>
                                <td class="py-2">
                                    {{ $r->user->name }}<br>
                                    <span class="text-gray-500">{{ $r->user->email }}</span>
                                </td>
                                <td class="py-2">
                                    {{ $r->rank->kyu }}級 / {{ $r->rank->dan ? $r->rank->dan . '段' : '無段' }}
                                    <div class="text-gray-500">level {{ $r->rank->level }}</div>
                                </td>
                                <td class="py-2">{{ optional($r->requested_at)->format('Y-m-d H:i') }}</td>
                                <td class="py-2 space-y-2">
                                    @if ($r->status === 'pending')
                                        <form method="POST" action="{{ route('admin.rank_requests.approve', $r) }}"
                                            class="space-y-1">
                                            @csrf
                                            <input name="comment" class="w-full border-gray-300 rounded"
                                                placeholder="コメント（任意）">
                                            <button
                                                class="px-3 py-1 rounded bg-indigo-600 text-white w-full">承認</button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.rank_requests.reject', $r) }}"
                                            class="space-y-1">
                                            @csrf
                                            <input name="comment" class="w-full border-gray-300 rounded"
                                                placeholder="コメント（任意）">
                                            <button class="px-3 py-1 rounded bg-gray-700 text-white w-full">却下</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500">処理済み</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div>{{ $requests->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
