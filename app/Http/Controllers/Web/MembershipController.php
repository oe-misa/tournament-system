<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MembershipController extends Controller
{
    public function create(Request $request, MembershipService $service)
    {
        $user = $request->user();
        $membershipPreview = $service->preview($user);

        return view('membership.create', compact('user', 'membershipPreview'));
    }

    public function store(Request $request, MembershipService $service)
    {
        try {
            $service->renew($request->user());
            return redirect()->route('membership.create')->with('status', '年間登録を更新しました');
        } catch (HttpException $e) {
            return redirect()->route('membership.create')->with('error', $e->getMessage());
        }
    }
}
