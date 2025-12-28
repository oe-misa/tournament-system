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
            ->orderBy('status')
            ->orderByDesc('id')
            ->paginate(30);

        return view('admin.rank_requests.index', compact('rankRequests'));
    }

    public function approve(Request $request, RankRequest $rankRequest)
    {
        if ((int)$rankRequest->status !== RankRequest::STATUS_PENDING) {
            return back()->with('status', 'この申請はすでに処理済みです');
        }

        $data = $request->validate([
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $rankRequest, $data) {
            $user = $rankRequest->user()->lockForUpdate()->first();

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

            // 申請承認＋履歴
            $rankRequest->status = RankRequest::STATUS_APPROVED;
            $rankRequest->approved_by = $request->user()->id;
            $rankRequest->approved_at = now();

            // ★管理者コメント
            $rankRequest->admin_comment = $data['admin_comment'] ?? null;

            $rankRequest->save();
        });

        return back()->with('status', '承認しました（ユーザー段位も更新済み）');
    }

    public function reject(Request $request, RankRequest $rankRequest)
    {
        if ((int)$rankRequest->status !== RankRequest::STATUS_PENDING) {
            return back()->with('status', 'この申請はすでに処理済みです');
        }

        $data = $request->validate([
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $rankRequest->status = RankRequest::STATUS_REJECTED;
        $rankRequest->rejected_by = $request->user()->id;
        $rankRequest->rejected_at = now();

        // ★管理者コメント
        $rankRequest->admin_comment = $data['admin_comment'] ?? null;

        $rankRequest->save();

        return back()->with('status', '却下しました');
    }
}
