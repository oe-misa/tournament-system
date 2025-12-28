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
    public function request(User $user, Rank $targetRank, ?string $comment = null): RankRequest
    {
        // 申請先が現在より下/同等は不可（原則）
        $currentLevel = $user->rank?->level ?? 0;
        if ($targetRank->level <= $currentLevel) {
            throw new HttpException(422, '現在より上の段位のみ申請できます');
        }

        // 既にpendingがあれば不可（運用上わかりやすい）
        $hasPending = RankRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            throw new HttpException(409, '審査中の申請が既に存在します');
        }

        return RankRequest::create([
            'user_id' => $user->id,
            'rank_id' => $targetRank->id,
            'status' => 'pending',
            'comment' => $comment,
            'requested_at' => now(),
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
        if ($request->status !== 'pending') {
            throw new HttpException(409, 'この申請は既に処理済みです');
        }

        return DB::transaction(function () use ($admin, $request, $comment) {
            $user = User::lockForUpdate()->findOrFail($request->user_id);

            // ユーザー段位更新
            $user->rank_id = $request->rank_id;
            $user->save();

            // 申請更新
            $request->status = 'approved';
            $request->approved_at = now();
            $request->processed_by = $admin->id;
            if ($comment) {
                // approvalsのcommentとして残したいなら運用次第。
                // 今回は既存commentに追記する簡易方式
                $request->comment = trim(($request->comment ?? '') . "\n[ADMIN] " . $comment);
            }
            $request->save();

            return $request->fresh(['rank', 'user', 'processedBy']);
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
        if ($request->status !== 'pending') {
            throw new HttpException(409, 'この申請は既に処理済みです');
        }

        $request->status = 'rejected';
        $request->approved_at = now();
        $request->processed_by = $admin->id;
        if ($comment) {
            $request->comment = trim(($request->comment ?? '') . "\n[ADMIN] " . $comment);
        }
        $request->save();

        return $request->fresh(['rank', 'user', 'processedBy']);
    }
}
