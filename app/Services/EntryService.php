<?php

namespace App\Services;

use App\Models\Entry;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntryService
{
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
            // 4) 定員チェック（capacityが設定されている場合）
            if (!is_null($tournament->capacity)) {
                // ロックで取りこぼし防止（高負荷時の二重カウント対策）
                $count = Entry::query()
                    ->where('tournament_id', $tournament->id)
                    ->lockForUpdate()
                    ->count();

                if ($count >= $tournament->capacity) {
                    throw new HttpException(403, '定員に達しています');
                }
            }

            // 5) 重複エントリー防止（DB UNIQUE + ここで事前チェック）
            $existing = Entry::query()
                ->where('user_id', $user->id)
                ->where('tournament_id', $tournament->id)
                ->first();

            if ($existing) {
                return $existing; // 既にあるならそのまま返す（冪等）
            }

            return Entry::create([
                'user_id' => $user->id,
                'tournament_id' => $tournament->id,
                'status' => 'entry',
            ]);
        });
    }

    /**
     * エントリーをキャンセル（必要なら）
     */
    public function cancel(User $user, Tournament $tournament): void
    {
        Entry::query()
            ->where('user_id', $user->id)
            ->where('tournament_id', $tournament->id)
            ->update(['status' => 'cancelled']);
    }
}
