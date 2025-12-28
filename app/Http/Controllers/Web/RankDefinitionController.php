<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rank;

class RankDefinitionController extends Controller
{
    public function show(Rank $rank)
    {
        $level = (int)$rank->level;

        return response()->json([
            'id' => $rank->id,
            'level' => $level,
            'label' => \App\Support\RankLabel::labelByLevel($level),
            'eligible_kyus' => \App\Support\RankLabel::eligibleKyus($level),
        ]);
    }
}
