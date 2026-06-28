<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\RankRequest;
use App\Models\Tournament;
use App\Models\User;

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

        $memberCount = User::query()->count();
        $draftTournamentCount = Tournament::query()
            ->where('status', Tournament::STATUS_DRAFT)
            ->count();

        return view('admin.dashboard', [
            'pendingRankRequestsCount' => $pendingRankRequestsCount,
            'missingResultsCount' => $missingResultsCount,
            'memberCount' => $memberCount,
            'draftTournamentCount' => $draftTournamentCount,
        ]);
    }
}
