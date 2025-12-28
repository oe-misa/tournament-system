<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\RankRequest;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 段位申請：未処理
        $pendingRankRequestsCount = RankRequest::query()
            ->where('status', 'pending')
            ->count();

        // 成績未入力：エントリーはあるが results が存在しない件数
        $missingResultsCount = Entry::query()
            ->where('entries.status', 'entry')
            ->leftJoin('results', function ($join) {
                $join->on('entries.tournament_id', '=', 'results.tournament_id')
                    ->on('entries.user_id', '=', 'results.user_id');
            })
            ->whereNull('results.id')
            ->count();

        return view('admin.dashboard', [
            'user' => $user,
            'pendingRankRequestsCount' => $pendingRankRequestsCount,
            'missingResultsCount' => $missingResultsCount,
        ]);
    }
}
