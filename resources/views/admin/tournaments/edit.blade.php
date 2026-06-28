<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">大会 編集</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="heian-card p-6 space-y-4">

                @if (session('status'))
                    <div class="p-3 bg-green-100 rounded">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="p-3 bg-red-100 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.tournaments.update', $tournament) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium">タイトル</label>
                        <input name="title" class="mt-1 w-full border-gray-300 rounded"
                            value="{{ old('title', $tournament->title) }}" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">説明</label>
                        <textarea name="description" class="mt-1 w-full border-gray-300 rounded" rows="4">{{ old('description', $tournament->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">公開状態</label>
                        <select name="status" class="mt-1 w-full border-gray-300 rounded">
                            <option value="draft" @selected((string) old('status', $tournament->status) === 'draft')>下書き</option>
                            <option value="recruiting" @selected((string) old('status', $tournament->status) === 'recruiting')>募集中</option>
                            <option value="closed" @selected((string) old('status', $tournament->status) === 'closed')>締切</option>
                            <option value="finished" @selected((string) old('status', $tournament->status) === 'finished')>終了</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">開催日</label>
                            <input type="date" name="event_date" class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('event_date', $tournament->event_date->format('Y-m-d')) }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium">申込締切（任意）</label>
                            <input type="datetime-local" name="entry_deadline"
                                class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('entry_deadline', $tournament->entry_deadline ? $tournament->entry_deadline->format('Y-m-d\TH:i') : '') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium">定員（任意）</label>
                            <input type="number" name="capacity" class="mt-1 w-full border-gray-300 rounded"
                                value="{{ old('capacity', $tournament->capacity) }}" min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium">参加条件（最低段位レベル）</label>
                            <select name="min_rank_level" class="mt-1 w-full border-gray-300 rounded">
                                @for ($i = 0; $i <= 10; $i++)
                                    <option value="{{ $i }}" @selected((int) old('min_rank_level', $tournament->min_rank_level) === $i)>
                                        {{ $i }}（{{ \App\Support\RankLabel::eligibleKyus($i) }}）
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.tournaments.index') }}" class="heian-btn-secondary">戻る</a>
                        <button class="heian-btn">更新</button>
                    </div>
                </form>

                <div class="border-t border-[#e6ded2] pt-5">
                    <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4 space-y-3">
                        <div>
                            <h3 class="text-lg font-semibold text-[#2f2a25]">削除</h3>
                            <p class="hub-muted text-sm mt-1">
                                削除すると大会情報と関連データの参照ができなくなります。必要な場合のみ実行してください。
                            </p>
                        </div>

                        <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament) }}">
                            @csrf
                            @method('DELETE')
                            <button class="heian-btn-danger"
                                onclick="return confirm('この大会を削除しますか？ この操作は取り消せません。')">
                                大会を削除
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border-t border-[#e6ded2] pt-6 space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-[#2f2a25]">エントリー一覧</h3>
                        <p class="hub-muted text-sm mt-1">管理者は締切日までは各エントリーをキャンセルできます。</p>
                    </div>

                    @if ($entries->isEmpty())
                        <div class="rounded-md border border-[#e6ded2] bg-[#faf7f1] p-4 hub-muted">エントリーはありません。</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="hub-table">
                                <thead>
                                    <tr>
                                        <th>参加者</th>
                                        <th>状態</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entries as $entry)
                                        <tr>
                                            <td>
                                                <div class="font-semibold">{{ $entry->user->name }}</div>
                                                <div class="hub-muted">{{ $entry->user->email }}</div>
                                            </td>
                                            <td>
                                                @if ($entry->status === \App\Models\Entry::STATUS_ENTRY)
                                                    <span class="heian-pill">エントリー済み</span>
                                                @else
                                                    <span class="heian-pill">キャンセル済み</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($entry->status === \App\Models\Entry::STATUS_ENTRY)
                                                    <form method="POST" action="{{ route('admin.tournaments.entries.destroy', [$tournament, $entry]) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="heian-btn-danger"
                                                            onclick="return confirm('このエントリーをキャンセルしますか？')">
                                                            キャンセル
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="hub-muted">処理済み</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
