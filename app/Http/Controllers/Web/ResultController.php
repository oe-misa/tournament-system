<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $results = Result::query()
            ->with('tournament:id,title,event_date')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id')
            ->paginate(20);

        return view('results.index', compact('results'));
    }
}
