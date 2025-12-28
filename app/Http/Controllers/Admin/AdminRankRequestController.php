<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\RankRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminRankRequestController extends Controller
{
    public function index()
    {
        $rankRequests = RankRequest::query()
            ->with([
                'user:id,name,email,rank_id',
                'rank:id,level',
                'requestedRank:id,level',
                'approver:id,name',
                'rejector:id,name',
            ])
            ->orderBy('status')     // 未処理→承認→却下
            ->orderByDesc('id')     // 新しい順
            ->paginate(30);

        return view('admin.rank_requests.index', compact('rankRequests'));
    }

    public function approve(Request $request, RankRequest $rankRequest)
    {
        if ((int)$rankRequest->status !== RankRequest::STATUS_PENDING) {
            return back()->with('status', 'この申請はすでに処理済みです');
        }

        DB::transaction(function () use ($request, $rankRequest) {
            $user = $rankRequest->user()->lockForUpdate()->first();

            // 申請段位を特定（requested_rank_id → rank_id → requested_level）
            $rank = null;

            if ($rankRequest->requested_rank_id) {
                $rank = Rank::find($rankRequest->requested_rank_id);
            }
            if (!$rank && $rankRequest->rank_id) {
                $rank = Rank::find($rankRequest->rank_id);
            }
            if (!$rank && $rankRequest->requested_level !== null) {
                $rank = Rank::where('level', (int)$rankRequest->requested_level)->first();
            }

            if (!$rank) {
                throw new \RuntimeException('申請の段位情報が不正です（rankが特定できません）');
            }

            // ユーザー段位更新
            $user->rank_id = $rank->id;
            $user->save();

            // 申請を承認（履歴情報も保存）
            $rankRequest->status = RankRequest::STATUS_APPROVED;
            $rankRequest->approved_by = $request->user()->id;
            $rankRequest->approved_at = now();
            $rankRequest->save();
        });

        return back()->with('status', '承認しました（ユーザー段位も更新済み）');
    }

    public function reject(Request $request, RankRequest $rankRequest)
    {
        if ((int)$rankRequest->status !== RankRequest::STATUS_PENDING) {
            return back()->with('status', 'この申請はすでに処理済みです');
        }

        $rankRequest->status = RankRequest::STATUS_REJECTED;
        $rankRequest->rejected_by = $request->user()->id;
        $rankRequest->rejected_at = now();
        $rankRequest->save();

        return back()->with('status', '却下しました');
    }
}
