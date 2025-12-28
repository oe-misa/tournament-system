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
        $requests = RankRequest::query()
            ->with(['user:id,name,email', 'rank:id,kyu,dan,level'])
            ->orderByRaw("status = 'pending' desc") // pendingを上に
            ->orderByDesc('requested_at')
            ->paginate(30);

        return view('admin.rank_requests.index', compact('requests'));
    }

    public function approve(Request $request, RankRequest $rankRequest, RankRequestService $service)
    {
        try {
            $service->approve($request->user(), $rankRequest, $request->input('comment'));
            return back()->with('status', '承認しました');
        } catch (HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, RankRequest $rankRequest, RankRequestService $service)
    {
        try {
            $service->reject($request->user(), $rankRequest, $request->input('comment'));
            return back()->with('status', '却下しました');
        } catch (HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
