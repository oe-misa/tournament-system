<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Tournament;
use App\Services\EntryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminTournamentController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->string('scope')->toString() ?: 'all';

        $query = Tournament::query();

        if ($scope === 'past') {
            $query->where(function ($q) {
                $q->where('status', Tournament::STATUS_FINISHED)
                    ->orWhere('event_date', '<', today());
            });
        } elseif ($scope === 'upcoming') {
            $query->where(function ($q) {
                $q->where('status', Tournament::STATUS_RECRUITING)
                    ->orWhere('event_date', '>=', today());
            });
        } elseif (in_array($scope, [
            Tournament::STATUS_DRAFT,
            Tournament::STATUS_RECRUITING,
            Tournament::STATUS_CLOSED,
            Tournament::STATUS_FINISHED,
        ], true)) {
            $query->where('status', $scope);
        }

        $tournaments = $query
            ->orderByRaw(
                'CASE WHEN status = ? OR event_date < ? THEN 0 ELSE 1 END, event_date DESC',
                [Tournament::STATUS_FINISHED, today()->toDateString()]
            )
            ->paginate(20)
            ->withQueryString();

        return view('admin.tournaments.index', compact('tournaments', 'scope'));
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
            'status' => ['nullable', 'string', 'in:' . implode(',', [
                Tournament::STATUS_DRAFT,
                Tournament::STATUS_RECRUITING,
                Tournament::STATUS_CLOSED,
                Tournament::STATUS_FINISHED,
            ])],
            'event_date' => ['required', 'date'],
            'entry_deadline' => ['nullable', 'date'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'min_rank_level' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $data['status'] = $data['status'] ?? Tournament::STATUS_RECRUITING;

        $tournament = Tournament::create($data);

        return redirect()->route('admin.tournaments.edit', $tournament)->with('status', '大会を作成しました');
    }

    public function edit(Tournament $tournament)
    {
        $entries = Entry::query()
            ->with('user:id,name,email')
            ->where('tournament_id', $tournament->id)
            ->orderBy('id')
            ->get();

        return view('admin.tournaments.edit', compact('tournament', 'entries'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:' . implode(',', [
                Tournament::STATUS_DRAFT,
                Tournament::STATUS_RECRUITING,
                Tournament::STATUS_CLOSED,
                Tournament::STATUS_FINISHED,
            ])],
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

    public function cancelEntry(Request $request, Tournament $tournament, Entry $entry, EntryService $service)
    {
        if ($entry->tournament_id !== $tournament->id) {
            abort(404);
        }

        try {
            $service->cancel($request->user(), $entry);
            return back()->with('status', 'エントリーをキャンセルしました');
        } catch (HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
