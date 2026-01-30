<?php

use App\Support\RankLabel;

it('formats rank labels for all levels', function () {
    expect(RankLabel::labelByLevel(-1))->toBe('無段（F〜E級）');
    expect(RankLabel::labelByLevel(0))->toBe('無段（F〜E級）');
    expect(RankLabel::labelByLevel(1))->toBe('初段（D級）');
    expect(RankLabel::labelByLevel(2))->toBe('弐段（C級）');
    expect(RankLabel::labelByLevel(3))->toBe('参段（B級）');
    expect(RankLabel::labelByLevel(4))->toBe('四段（A級）');
    expect(RankLabel::labelByLevel(5))->toBe('五段（A級）');
    expect(RankLabel::labelByLevel(6))->toBe('六段（A級）');
    expect(RankLabel::labelByLevel(7))->toBe('七段（A級）');
    expect(RankLabel::labelByLevel(8))->toBe('八段（A級）');
    expect(RankLabel::labelByLevel(9))->toBe('九段（A級）');
    expect(RankLabel::labelByLevel(10))->toBe('十段（A級）');
    expect(RankLabel::labelByLevel(11))->toBe('十段（A級）');
});

it('formats eligible kyus by min level', function () {
    expect(RankLabel::eligibleKyus(-1))->toBe('A,B,C,D,F〜E級');
    expect(RankLabel::eligibleKyus(0))->toBe('A,B,C,D,F〜E級');
    expect(RankLabel::eligibleKyus(1))->toBe('A,B,C,D級');
    expect(RankLabel::eligibleKyus(2))->toBe('A,B,C級');
    expect(RankLabel::eligibleKyus(3))->toBe('A,B級');
    expect(RankLabel::eligibleKyus(4))->toBe('A級');
    expect(RankLabel::eligibleKyus(10))->toBe('A級');
});
