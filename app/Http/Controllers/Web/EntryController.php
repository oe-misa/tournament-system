<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\EntryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntryController extends Controller
{
    public function store(Request $request, Tournament $tournament, EntryService $service)
    {
        try {
            $service->entry($request->user(), $tournament);
            return redirect()->route('tournaments.show', $tournament)->with('status', 'エントリーしました');
        } catch (HttpException $e) {
            return redirect()->route('tournaments.show', $tournament)->with('error', $e->getMessage());
        }
    }
}
