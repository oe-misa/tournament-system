<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [];

        // 無段（F/E） level=0
        $rows[] = ['kyu' => 'F', 'dan' => null, 'level' => 0, 'created_at' => $now, 'updated_at' => $now];
        $rows[] = ['kyu' => 'E', 'dan' => null, 'level' => 0, 'created_at' => $now, 'updated_at' => $now];

        // D級=初段(1), C級=弐段(2), B級=参段(3)
        $rows[] = ['kyu' => 'D', 'dan' => 1, 'level' => 1, 'created_at' => $now, 'updated_at' => $now];
        $rows[] = ['kyu' => 'C', 'dan' => 2, 'level' => 2, 'created_at' => $now, 'updated_at' => $now];
        $rows[] = ['kyu' => 'B', 'dan' => 3, 'level' => 3, 'created_at' => $now, 'updated_at' => $now];

        // A級=四段〜十段 level=4〜10
        for ($dan = 4; $dan <= 10; $dan++) {
            $rows[] = ['kyu' => 'A', 'dan' => $dan, 'level' => $dan, 'created_at' => $now, 'updated_at' => $now];
        }

        DB::table('ranks')->upsert(
            $rows,
            ['kyu', 'dan'],  // unique key
            ['level', 'updated_at']
        );
    }
}
