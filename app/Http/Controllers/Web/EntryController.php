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

    public function destroy(Request $request, Tournament $tournament, EntryService $service)
    {
        $entry = $service->getEntryForUser($request->user(), $tournament);

        if (!$entry) {
            return redirect()->route('tournaments.show', $tournament)->with('error', 'エントリー情報が見つかりません');
        }

        try {
            $cancelled = $service->cancel($request->user(), $entry);

            $message = $cancelled->isCancelled()
                ? 'エントリーをキャンセルしました'
                : 'すでにキャンセルされています';

            return redirect()->route('tournaments.show', $tournament)->with('status', $message);
        } catch (HttpException $e) {
            return redirect()->route('tournaments.show', $tournament)->with('error', $e->getMessage());
        }
    }
}
