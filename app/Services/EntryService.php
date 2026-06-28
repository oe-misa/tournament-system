<?php

namespace App\Services;

use App\Models\Entry;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntryService
{
    public function getEntryForUser(User $user, Tournament $tournament): ?Entry
    {
        return Entry::query()
            ->where('user_id', $user->id)
            ->where('tournament_id', $tournament->id)
            ->first();
    }

    public function canCancel(User $actor, Entry $entry): bool
    {
        try {
            $this->assertCanCancel($actor, $entry);

            return true;
        } catch (HttpException) {
            return false;
        }
    }

    public function cancelMessage(User $actor, Entry $entry): ?string
    {
        try {
            $this->assertCanCancel($actor, $entry);

            return null;
        } catch (HttpException $e) {
            return $e->getMessage();
        }
    }

    /**
     * 大会へエントリーする
     */
    public function entry(User $user, Tournament $tournament): Entry
    {
        // 1) 年間登録チェック（期限が切れていたら不可）
        $expiresAt = $user->membership_expires_at;
        if (!$expiresAt || $expiresAt->isPast()) {
            throw new HttpException(403, '年間登録が未登録、または期限切れです');
        }

        // 2) 段位チェック（大会ごとの min_rank_level を自動適用）
        $userLevel = $user->rank?->level ?? 0;
        if ($userLevel < $tournament->min_rank_level) {
            throw new HttpException(403, '参加条件（段位）を満たしていません');
        }

        // 3) 締切チェック（設定されている場合）
        if ($tournament->entry_deadline && now()->greaterThan($tournament->entry_deadline)) {
            throw new HttpException(403, 'エントリー締切を過ぎています');
        }

        return DB::transaction(function () use ($user, $tournament) {
            $existing = Entry::query()
                ->where('user_id', $user->id)
                ->where('tournament_id', $tournament->id)
                ->lockForUpdate()
                ->first();

            if ($existing && $existing->isEntry()) {
                return $existing; // 既にあるならそのまま返す（冪等）
            }

            // 4) 定員チェック（capacityが設定されている場合）
            if (!is_null($tournament->capacity)) {
                // ロックで取りこぼし防止（高負荷時の二重カウント対策）
                $count = Entry::query()
                    ->where('tournament_id', $tournament->id)
                    ->where('status', Entry::STATUS_ENTRY)
                    ->lockForUpdate()
                    ->count();

                if ($count >= $tournament->capacity) {
                    throw new HttpException(403, '定員に達しています');
                }
            }

            if ($existing) {
                $existing->update(['status' => Entry::STATUS_ENTRY]);

                return $existing->refresh();
            }

            return Entry::create([
                'user_id' => $user->id,
                'tournament_id' => $tournament->id,
                'status' => Entry::STATUS_ENTRY,
            ]);
        });
    }

    public function cancel(User $actor, Entry $entry): Entry
    {
        return DB::transaction(function () use ($actor, $entry) {
            $entry = Entry::query()
                ->with('tournament')
                ->lockForUpdate()
                ->findOrFail($entry->id);

            $this->assertCanCancel($actor, $entry);

            if ($entry->isCancelled()) {
                return $entry;
            }

            $entry->update(['status' => Entry::STATUS_CANCELLED]);

            return $entry->refresh();
        });
    }

    private function assertCanCancel(User $actor, Entry $entry): void
    {
        $tournament = $entry->tournament;

        if (!$tournament) {
            $tournament = $entry->tournament()->firstOrFail();
        }

        if ($actor->id !== $entry->user_id && !$actor->is_admin) {
            throw new HttpException(403, 'このエントリーをキャンセルする権限がありません');
        }

        if (!$tournament->entry_deadline) {
            return;
        }

        if ($actor->is_admin) {
            if (now()->greaterThan($tournament->entry_deadline)) {
                throw new HttpException(403, 'エントリー締切を過ぎているためキャンセルできません');
            }

            return;
        }

        $memberDeadline = $tournament->entry_deadline->copy()->subDays(10);

        if (now()->greaterThan($memberDeadline)) {
            throw new HttpException(403, '締切10日前を過ぎたため管理者のみキャンセルできます');
        }
    }
}
