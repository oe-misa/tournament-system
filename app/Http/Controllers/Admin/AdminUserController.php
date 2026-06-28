<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = User::query()
            ->with('rank:id,kyu,dan,level')
            ->withCount(['entries', 'results', 'rankRequests'])
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(30)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function show(User $user)
    {
        $user->load([
            'rank:id,kyu,dan,level',
            'entries.tournament:id,title,event_date',
            'results.tournament:id,title,event_date',
            'memberships',
            'rankRequests.rank:id,kyu,dan,level',
            'rankRequests.requestedRank:id,kyu,dan,level',
            'rankRequests.approver:id,name',
            'rankRequests.rejector:id,name',
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $ranks = Rank::query()->orderBy('level')->get();

        return view('admin.users.edit', compact('user', 'ranks'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'rank_id' => ['nullable', 'integer', 'exists:ranks,id'],
            'membership_expires_at' => ['nullable', 'date'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->rank_id = $data['rank_id'] ?? null;
        $user->membership_expires_at = $data['membership_expires_at'] ?? null;

        $isAdmin = (bool) ($data['is_admin'] ?? false);
        if ($request->user()->id === $user->id) {
            $isAdmin = true;
        }
        $user->is_admin = $isAdmin;

        $user->save();

        return redirect()->route('admin.users.edit', $user)->with('status', '会員情報を更新しました');
    }
}
