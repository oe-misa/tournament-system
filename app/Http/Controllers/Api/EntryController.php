<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\EntryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EntryController extends Controller
{
    public function store(Request $request, Tournament $tournament, EntryService $entryService)
    {
        try {
            $entry = $entryService->entry($request->user(), $tournament);

            return response()->json([
                'message' => 'エントリーしました',
                'entry' => $entry,
            ], 201);
        } catch (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }
    }

    public function destroy(Request $request, Tournament $tournament, EntryService $entryService)
    {
        $entry = $entryService->getEntryForUser($request->user(), $tournament);

        if (!$entry) {
            return response()->json([
                'message' => 'エントリー情報が見つかりません',
            ], 404);
        }

        try {
            $cancelled = $entryService->cancel($request->user(), $entry);

            return response()->json([
                'message' => $cancelled->isCancelled() ? 'エントリーをキャンセルしました' : 'すでにキャンセルされています',
                'entry' => $cancelled,
            ]);
        } catch (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }
    }
}
