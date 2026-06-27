<x-app-layout>
    <div class="hub-page-narrow space-y-6">
        <div>
            <h1 class="hub-title">段位申請</h1>
            <p class="hub-muted mt-1">現在の段位以上の申請を受け付けます。</p>
        </div>

        <div class="heian-card p-6 space-y-5">

                {{-- エラー表示 --}}
                @if ($errors->any())
                    <div class="hub-alert-danger">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4 text-sm">
                    <div class="hub-muted font-semibold">現在の段位</div>
                    <div class="mt-1 text-lg font-bold">{{ \App\Support\RankLabel::labelByLevel((int) $currentLevel) }}</div>
                </div>

                {{-- 選択プレビュー（定義から取得して表示） --}}
                <div id="rankPreview"
                    class="hidden rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4 text-sm text-[#34251f]">
                    <div class="font-semibold">申請内容プレビュー</div>
                    <div class="mt-1">
                        申請段位：<span id="previewLabel" class="font-bold"></span>
                    </div>
                    <div class="hub-muted mt-1 text-xs">
                        参加条件表示例：<span id="previewEligible"></span>
                    </div>
                </div>

                <form method="POST" action="{{ route('rank_requests.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">申請する段位</label>

                        <select id="requested_rank_id" name="requested_rank_id"
                            class="mt-1 w-full" required>
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

                        <div class="hub-muted mt-1 text-xs">
                            選択すると段位の定義を取得し、プレビューに反映します。
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">備考（任意）</label>
                        <textarea name="note" rows="4" class="mt-1 w-full">{{ old('note') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('dashboard') }}" class="heian-btn-secondary">戻る</a>
                        <button class="heian-btn">申請する</button>
                    </div>
                </form>

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
