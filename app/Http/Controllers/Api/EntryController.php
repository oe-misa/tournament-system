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
}
