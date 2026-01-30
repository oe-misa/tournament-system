<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RankRequest;
use App\Services\RankRequestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

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

    public function approve(Request $request, RankRequest $rankRequest, RankRequestService $service)
    {
        $data = $request->validate([
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->approve($request->user(), $rankRequest, $data['admin_comment'] ?? null);
        } catch (HttpException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', '承認しました（ユーザー段位も更新済み）');
    }

    public function reject(Request $request, RankRequest $rankRequest, RankRequestService $service)
    {
        $data = $request->validate([
            'admin_comment' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $service->reject($request->user(), $rankRequest, $data['admin_comment'] ?? null);
        } catch (HttpException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', '却下しました');
    }
}
