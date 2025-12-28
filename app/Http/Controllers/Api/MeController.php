<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('rank');

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'membership_expires_at' => $user->membership_expires_at,
            'is_admin' => (bool) $user->is_admin,
            'rank' => $user->rank ? [
                'id' => $user->rank->id,
                'kyu' => $user->rank->kyu,
                'dan' => $user->rank->dan,
                'level' => $user->rank->level,
            ] : null,
        ]);
    }
}
