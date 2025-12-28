<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\RankRequest;
use Illuminate\Http\Request;

class RankRequestController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user()->loadMissing('rank');
        $currentLevel = (int)($user->rank?->level ?? 0);

        $ranks = Rank::query()->orderBy('level')->get();

        return view('rank_requests.create', [
            'ranks' => $ranks,
            'currentLevel' => $currentLevel,
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user()->loadMissing('rank');
        $currentLevel = (int)($user->rank?->level ?? 0);

        // 未処理があれば二重申請防止
        $existsPending = RankRequest::query()
            ->where('user_id', $user->id)
            ->pending()
            ->exists();

        if ($existsPending) {
            return back()
                ->withErrors(['duplicate' => '未処理の申請がすでにあります。承認/却下をお待ちください。'])
                ->withInput();
        }

        $data = $request->validate([
            'requested_rank_id' => ['required', 'integer', 'exists:ranks,id'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $requestedRank = Rank::find($data['requested_rank_id']);
        if (!$requestedRank) {
            return back()->withErrors(['requested_rank_id' => '申請する段位が不正です。'])->withInput();
        }

        // サーバ側でも「現在段位未満」を拒否
        if ((int)$requestedRank->level < $currentLevel) {
            return back()->withErrors(['requested_rank_id' => '現在の段位より下は申請できません。'])->withInput();
        }

        RankRequest::create([
            'user_id' => $user->id,
            'status' => RankRequest::STATUS_PENDING, // ★数値で統一
            'requested_at' => now(),

            // DB側必須の rank_id
            'rank_id' => $requestedRank->id,

            // 将来用・表示用
            'requested_rank_id' => $requestedRank->id,
            'requested_level' => (int)$requestedRank->level,

            'note' => $data['note'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('status', '段位申請を受け付けました（管理者の承認待ち）');
    }
}
