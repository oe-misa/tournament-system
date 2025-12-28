<?php

namespace App\Support;

class RankLabel
{
    /**
     * min_rank_level から「参加可能な級表示」を返す
     * 例: 3 -> "B級以上（B・A）"
     *     4 -> "A級"
     *     0 -> "全級可"
     */
    public static function eligibleKyus(int $minLevel): string
    {
        if ($minLevel <= 0) {
            return '全級可';
        }

        if ($minLevel >= 4) {
            return 'A級';
        }

        // level 1〜3
        $map = [
            1 => 'D',
            2 => 'C',
            3 => 'B',
        ];

        $from = $map[$minLevel] ?? 'B';

        return "{$from}級以上（{$from}・A）";
    }
}
