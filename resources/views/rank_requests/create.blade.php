<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">段位申請</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">

                {{-- エラー表示 --}}
                @if ($errors->any())
                    <div class="p-3 bg-red-100 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="p-3 bg-gray-50 border rounded text-sm text-gray-700">
                    現在の段位：
                    <span class="font-semibold">{{ \App\Support\RankLabel::labelByLevel((int) $currentLevel) }}</span>
                    <div class="text-xs text-gray-500 mt-1">
                        ※ 申請できるのは「現在の段位以上」です（現在より下は選択できません）
                    </div>
                </div>

                {{-- 選択プレビュー（定義から取得して表示） --}}
                <div id="rankPreview"
                    class="p-3 bg-indigo-50 border border-indigo-200 rounded text-sm text-indigo-900 hidden">
                    <div class="font-semibold">申請内容プレビュー</div>
                    <div class="mt-1">
                        申請段位：<span id="previewLabel" class="font-bold"></span>
                    </div>
                    <div class="mt-1 text-xs text-indigo-800">
                        参加条件表示例：<span id="previewEligible"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('rank_requests.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700">申請する段位（選択）</label>

                        <select id="requested_rank_id" name="requested_rank_id"
                            class="mt-1 w-full border-gray-300 rounded" required>
                            <option value="">-- 選択してください --</option>

                            @foreach ($ranks as $rank)
                                @php
                                    $level = (int) $rank->level;
                                    $disabled = $level < (int) $currentLevel;
                                @endphp
                                <option value="{{ $rank->id }}" @selected((int) old('requested_rank_id') === (int) $rank->id)
                                    @disabled($disabled)>
                                    {{ \App\Support\RankLabel::labelByLevel($level) }}
                                    @if ($disabled)
                                        （選択不可）
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="text-xs text-gray-500 mt-1">
                            ※ 選択すると段位の定義を取得し、プレビューに反映します
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <label class="block text-sm font-medium text-gray-700">備考（任意）</label>
                        <textarea name="note" rows="4" class="mt-1 w-full border-gray-300 rounded">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded border">戻る</a>
                        <button class="px-4 py-2 rounded bg-indigo-600 text-white">申請する</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        (function() {
            const select = document.getElementById('requested_rank_id');
            const preview = document.getElementById('rankPreview');
            const labelEl = document.getElementById('previewLabel');
            const eligibleEl = document.getElementById('previewEligible');

            async function updatePreview() {
                const id = select.value;
                if (!id) {
                    preview.classList.add('hidden');
                    labelEl.textContent = '';
                    eligibleEl.textContent = '';
                    return;
                }

                try {
                    const res = await fetch(`/rank-definitions/${id}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) throw new Error('failed');

                    const data = await res.json();
                    labelEl.textContent = data.label ?? '';
                    eligibleEl.textContent = data.eligible_kyus ?? '';

                    preview.classList.remove('hidden');
                } catch (e) {
                    preview.classList.add('hidden');
                }
            }

            select.addEventListener('change', updatePreview);

            // 初期表示（validationで戻ってきた時など）
            updatePreview();
        })();
    </script>
</x-app-layout>
