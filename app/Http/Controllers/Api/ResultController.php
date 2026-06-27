<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $results = Result::query()
            ->select('results.*')
            ->join('tournaments', 'results.tournament_id', '=', 'tournaments.id')
            ->with('tournament:id,title,event_date')
            ->where('results.user_id', $user->id)
            ->orderByDesc('tournaments.event_date')
            ->orderByDesc('results.id')
            ->paginate(20);

        return response()->json($results);
    }
}
