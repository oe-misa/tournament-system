<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MembershipController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        return view('membership.create', compact('user'));
    }

    public function store(Request $request, MembershipService $service)
    {
        $validated = $request->validate([
            'years' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        try {
            $service->renew($request->user(), (int)$validated['years']);
            return redirect()->route('membership.create')->with('status', '年間登録を更新しました');
        } catch (HttpException $e) {
            return redirect()->route('membership.create')->with('error', $e->getMessage());
        }
    }
}
