<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\OmikujiDraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OmikujiController extends Controller
{
    private const RESULTS = ['大吉', '吉', '中吉', '小吉', '凶'];

    public function draw(Request $request)
    {
        $user = $request->user();
        $today = now()->toDateString();

        $existing = OmikujiDraw::query()
            ->where('user_id', $user->id)
            ->where('drawn_on', $today)
            ->first();

        if ($existing) {
            return redirect()->route('dashboard')->with('status', '本日の御神籤は既に引いています');
        }

        try {
            $draw = DB::transaction(function () use ($user, $today) {
                $locked = OmikujiDraw::query()
                    ->where('user_id', $user->id)
                    ->where('drawn_on', $today)
                    ->lockForUpdate()
                    ->first();

                if ($locked) {
                    throw new HttpException(409, '本日の御神籤は既に引いています');
                }

                $result = self::RESULTS[array_rand(self::RESULTS)];

                return OmikujiDraw::create([
                    'user_id' => $user->id,
                    'result' => $result,
                    'drawn_on' => $today,
                ]);
            });
        } catch (HttpException $e) {
            return redirect()->route('dashboard')->with('status', $e->getMessage());
        }

        return redirect()->route('dashboard')->with('status', '本日の御神籤は「' . $draw->result . '」でした');
    }
}
