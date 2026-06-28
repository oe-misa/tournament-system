<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\EntryService;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index()
    {
        $tournaments = Tournament::query()
            ->where('status', '!=', Tournament::STATUS_DRAFT)
            ->orderBy('event_date')
            ->paginate(20);

        return view('tournaments.index', compact('tournaments'));
    }

    public function show(Request $request, Tournament $tournament, EntryService $entryService)
    {
        if ($tournament->status === Tournament::STATUS_DRAFT && !$request->user()->is_admin) {
            abort(404);
        }

        $entry = $entryService->getEntryForUser($request->user(), $tournament);
        $cancelable = $entry ? $entryService->canCancel($request->user(), $entry) : false;
        $cancelMessage = $entry ? $entryService->cancelMessage($request->user(), $entry) : null;

        return view('tournaments.show', compact('tournament', 'entry', 'cancelable', 'cancelMessage'));
    }
}
