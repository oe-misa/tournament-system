<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $results = Result::query()
            ->select('results.*')
            ->join('tournaments', 'results.tournament_id', '=', 'tournaments.id')
            ->with('tournament:id,title,event_date')
            ->where('results.user_id', $request->user()->id)
            ->orderByDesc('tournaments.event_date')
            ->orderByDesc('results.id')
            ->paginate(20);

        return view('results.index', compact('results'));
    }
}
