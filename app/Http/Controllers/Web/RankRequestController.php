<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\RankRequest;
use App\Services\RankRequestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RankRequestController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user()->loadMissing('rank');
        $currentLevel = (int)($user->rank?->level ?? 0);

        $ranks = Rank::query()->orderBy('level')->get();

        return view('rank_requests.create', [
            'ranks' => $ranks,
            'currentLevel' => $currentLevel,
        ]);
    }

    public function store(Request $request, RankRequestService $service)
    {
        $user = $request->user()->loadMissing('rank');

        $data = $request->validate([
            'requested_rank_id' => ['required', 'integer', 'exists:ranks,id'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $requestedRank = Rank::findOrFail($data['requested_rank_id']);

        try {
            $service->request($user, $requestedRank, $data['note'] ?? null);
        } catch (HttpException $e) {
            return back()->withErrors(['requested_rank_id' => $e->getMessage()])->withInput();
        }

        return redirect()->route('dashboard')->with('status', '段位申請を受け付けました（管理者の承認待ち）');
    }

    public function history(Request $request)
    {
        $user = $request->user();

        $rankRequests = RankRequest::query()
            ->where('user_id', $user->id)
            ->with([
                'rank:id,level',
                'requestedRank:id,level',
                'approver:id,name',
                'rejector:id,name',
            ])
            ->orderByDesc('id')
            ->paginate(20);

        return view('rank_requests.history', [
            'rankRequests' => $rankRequests,
        ]);
    }
}
