<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;

class AdminTournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::query()
            ->orderByDesc('event_date')
            ->paginate(20);

        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'entry_deadline' => ['nullable', 'date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'min_rank_level' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $tournament = Tournament::create($data);

        return redirect()->route('admin.tournaments.edit', $tournament)->with('status', '大会を作成しました');
    }

    public function edit(Tournament $tournament)
    {
        return view('admin.tournaments.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'entry_deadline' => ['nullable', 'date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'min_rank_level' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $tournament->update($data);

        return redirect()->route('admin.tournaments.edit', $tournament)->with('status', '更新しました');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return redirect()->route('admin.tournaments.index')->with('status', '削除しました');
    }

    public function show(Tournament $tournament)
    {
        return redirect()->route('admin.tournaments.edit', $tournament);
    }
}
