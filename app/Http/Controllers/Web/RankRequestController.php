<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Services\RankRequestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RankRequestController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user()->load('rank');

        $currentLevel = $user->rank?->level ?? 0;

        // 申請可能な候補（現在より上）
        $ranks = Rank::query()
            ->where('level', '>', $currentLevel)
            ->orderBy('level')
            ->get();

        return view('rank_requests.create', compact('user', 'ranks'));
    }

    public function store(Request $request, RankRequestService $service)
    {
        $validated = $request->validate([
            'rank_id' => ['required', 'integer', 'exists:ranks,id'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $rank = Rank::findOrFail($validated['rank_id']);
            $service->request($request->user(), $rank, $validated['comment'] ?? null);

            return redirect()->route('rank_requests.create')->with('status', '段位申請を送信しました');
        } catch (HttpException $e) {
            return redirect()->route('rank_requests.create')->with('error', $e->getMessage());
        }
    }
}
