<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Result;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminResultController extends Controller
{
    public function edit(Tournament $tournament)
    {
        $entries = Entry::query()
            ->with('user:id,name,email')
            ->where('tournament_id', $tournament->id)
            ->where('status', 'entry')
            ->orderBy('id')
            ->get();

        $results = Result::query()
            ->where('tournament_id', $tournament->id)
            ->get()
            ->keyBy('user_id');

        return view('admin.results.edit', compact('tournament', 'entries', 'results'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $data = $request->validate([
            'results' => ['required', 'array'],
            'results.*.placing' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'results.*.score' => ['nullable', 'integer', 'min:-999999', 'max:999999'],
            'results.*.note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($data, $tournament) {
            foreach ($data['results'] as $userId => $row) {
                $allEmpty = ($row['placing'] ?? null) === null
                    && ($row['score'] ?? null) === null
                    && empty($row['note'] ?? null);

                if ($allEmpty) continue;

                Result::updateOrCreate(
                    ['tournament_id' => $tournament->id, 'user_id' => (int)$userId],
                    [
                        'placing' => $row['placing'] ?? null,
                        'score' => $row['score'] ?? null,
                        'note' => $row['note'] ?? null,
                    ]
                );
            }
        });

        return back()->with('status', '成績を保存しました');
    }
}
