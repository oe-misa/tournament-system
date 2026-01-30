<?php

namespace App\Services;

use App\Models\Rank;
use App\Models\RankRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RankRequestService
{
    /**
     * 段位申請を作成
     */
    public function request(User $user, Rank $targetRank, ?string $note = null): RankRequest
    {
        // 申請先が現在より下は不可
        $currentLevel = $user->rank?->level ?? 0;
        if ($targetRank->level < $currentLevel) {
            throw new HttpException(422, '現在の段位より下は申請できません');
        }

        // 既にpendingがあれば不可（運用上わかりやすい）
        $hasPending = RankRequest::query()
            ->where('user_id', $user->id)
            ->where('status', RankRequest::STATUS_PENDING)
            ->exists();

        if ($hasPending) {
            throw new HttpException(409, '審査中の申請が既に存在します');
        }

        return RankRequest::create([
            'user_id' => $user->id,
            'rank_id' => $targetRank->id,
            'status' => RankRequest::STATUS_PENDING,
            'requested_at' => now(),
            'requested_rank_id' => $targetRank->id,
            'requested_level' => (int)$targetRank->level,
            'note' => $note,
        ]);
    }

    /**
     * 管理者が承認
     */
    public function approve(User $admin, RankRequest $request, ?string $comment = null): RankRequest
    {
        if (!$admin->is_admin) {
            throw new HttpException(403, '管理者のみ実行できます');
        }
        if ($request->status !== RankRequest::STATUS_PENDING) {
            throw new HttpException(409, 'この申請は既に処理済みです');
        }

        return DB::transaction(function () use ($admin, $request, $comment) {
            $user = User::lockForUpdate()->findOrFail($request->user_id);

            // ユーザー段位更新
            $user->rank_id = $request->rank_id;
            $user->save();

            // 申請更新
            $request->status = RankRequest::STATUS_APPROVED;
            $request->approved_at = now();
            $request->approved_by = $admin->id;
            $request->admin_comment = $comment;
            $request->save();

            return $request->fresh(['rank', 'user', 'approver', 'rejector']);
        });
    }

    /**
     * 管理者が却下
     */
    public function reject(User $admin, RankRequest $request, ?string $comment = null): RankRequest
    {
        if (!$admin->is_admin) {
            throw new HttpException(403, '管理者のみ実行できます');
        }
        if ($request->status !== RankRequest::STATUS_PENDING) {
            throw new HttpException(409, 'この申請は既に処理済みです');
        }

        $request->status = RankRequest::STATUS_REJECTED;
        $request->rejected_at = now();
        $request->rejected_by = $admin->id;
        $request->admin_comment = $comment;
        $request->save();

        return $request->fresh(['rank', 'user', 'approver', 'rejector']);
    }
}
