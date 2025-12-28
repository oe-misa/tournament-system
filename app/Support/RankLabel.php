<?php

namespace App\Support;

class RankLabel
{
    /**
     * 段位レベル(0-10)を、人が読む段位/級表記に変換
     *
     * 仕様:
     * - level 0: 無段（F〜E級）
     * - level 1: 初段（D級）
     * - level 2: 弐段（C級）
     * - level 3: 参段（B級）
     * - level 4-10: 四段〜十段（A級）
     */
    public static function labelByLevel(int $level): string
    {
        return match (true) {
            $level <= 0 => '無段（F〜E級）',
            $level === 1 => '初段（D級）',
            $level === 2 => '弐段（C級）',
            $level === 3 => '参段（B級）',
            $level === 4 => '四段（A級）',
            $level === 5 => '五段（A級）',
            $level === 6 => '六段（A級）',
            $level === 7 => '七段（A級）',
            $level === 8 => '八段（A級）',
            $level === 9 => '九段（A級）',
            $level >= 10 => '十段（A級）',
            default => "不明（Lv {$level}）",
        };
    }

    /**
     * 大会参加条件表示（最低level以上が参加可）→「A,B級」など
     * 例:
     * - min=0 => A,B,C,D,F〜E級（全員）
     * - min=1 => A,B,C,D級
     * - min=2 => A,B,C級
     * - min=3 => A,B級
     * - min=4 => A級
     */
    public static function eligibleKyus(int $minLevel): string
    {
        return match (true) {
            $minLevel <= 0 => 'A,B,C,D,F〜E級',
            $minLevel === 1 => 'A,B,C,D級',
            $minLevel === 2 => 'A,B,C級',
            $minLevel === 3 => 'A,B級',
            $minLevel >= 4 => 'A級',
            default => '不明',
        };
    }
}
