<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\RankRequest;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user()->loadMissing('rank');

        $pendingRankRequestsCount = 0;
        $missingResultsCount = 0;

        if ($user->is_admin) {
            $pendingRankRequestsCount = RankRequest::query()
                ->where('status', 'pending')
                ->count();

            $missingResultsCount = Entry::query()
                ->where('entries.status', 'entry')
                ->leftJoin('results', function ($join) {
                    $join->on('entries.tournament_id', '=', 'results.tournament_id')
                        ->on('entries.user_id', '=', 'results.user_id');
                })
                ->whereNull('results.id')
                ->count();
        }

        return view('dashboard', [
            'user' => $user,
            'pendingRankRequestsCount' => $pendingRankRequestsCount,
            'missingResultsCount' => $missingResultsCount,
        ]);
    }
}
