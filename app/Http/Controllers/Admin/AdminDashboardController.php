<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\RankRequest;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingRankRequestsCount = RankRequest::query()
            ->pending()
            ->count();

        $missingResultsCount = Entry::query()
            ->where('entries.status', 'entry')
            ->leftJoin('results', function ($join) {
                $join->on('entries.tournament_id', '=', 'results.tournament_id')
                    ->on('entries.user_id', '=', 'results.user_id');
            })
            ->whereNull('results.id')
            ->count();

        return view('admin.dashboard', [
            'pendingRankRequestsCount' => $pendingRankRequestsCount,
            'missingResultsCount' => $missingResultsCount,
        ]);
    }
}
