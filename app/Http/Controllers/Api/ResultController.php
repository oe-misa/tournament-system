<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $results = Result::query()
            ->with('tournament:id,title,event_date')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json($results);
    }
}
