<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Services\RankRequestService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RankRequestController extends Controller
{
    public function store(Request $request, RankRequestService $service)
    {
        $validated = $request->validate([
            'rank_id' => ['required', 'integer', 'exists:ranks,id'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $rank = Rank::findOrFail($validated['rank_id']);
            $rr = $service->request($request->user(), $rank, $validated['comment'] ?? null);

            return response()->json([
                'message' => '段位申請を受け付けました',
                'rank_request' => $rr,
            ], 201);
        } catch (HttpException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }
    }
}
