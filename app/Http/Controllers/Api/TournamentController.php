<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Request $request)
    {
        // まずはシンプルに一覧（必要なら絞り込み・検索を後で追加）
        return Tournament::query()
            ->orderBy('event_date')
            ->paginate(20);
    }

    public function show(Tournament $tournament)
    {
        return response()->json($tournament);
    }
}
