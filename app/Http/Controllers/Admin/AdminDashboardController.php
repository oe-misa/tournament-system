<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\RankRequest;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Membership;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingRankRequestsCount = RankRequest::query()
            ->pending()
            ->count();

        $missingResultsCount = Entry::query()
            ->where('entries.status', 'entry')
            ->leftJoin('results', function ($join) {
                $join->on('entries.tournament_id', '=', 'results.tournament_id')
                    ->on('entries.user_id', '=', 'results.user_id');
            })
            ->whereNull('results.id')
            ->count();

        $memberCount = User::query()->count();
        $draftTournamentCount = Tournament::query()
            ->where('status', Tournament::STATUS_DRAFT)
            ->count();
        $membershipCounts = collect([Membership::STATUS_PENDING_PAYMENT,Membership::STATUS_PAYMENT_CONFIRMED,Membership::STATUS_APPROVED,Membership::STATUS_REJECTED])->mapWithKeys(fn($s)=>[$s=>Membership::where('status',$s)->count()]);
        $activeMemberCount=User::where('account_status','active')->whereDate('membership_expires_at','>=',today())->count();
        $expiredMemberCount=User::where('account_status','active')->where(fn($q)=>$q->whereNull('membership_expires_at')->orWhereDate('membership_expires_at','<',today()))->count();
        $overduePaymentCount = Membership::where('status', Membership::STATUS_PENDING_PAYMENT)->where('created_at', '<=', now()->subDays(7))->count();
        $overdueApprovalCount = Membership::where('status', Membership::STATUS_PAYMENT_CONFIRMED)->where('payment_confirmed_at', '<=', now()->subDays(3))->count();

        return view('admin.dashboard', [
            'pendingRankRequestsCount' => $pendingRankRequestsCount,
            'missingResultsCount' => $missingResultsCount,
            'memberCount' => $memberCount,
            'draftTournamentCount' => $draftTournamentCount,
            'membershipCounts'=>$membershipCounts,'activeMemberCount'=>$activeMemberCount,'expiredMemberCount'=>$expiredMemberCount,
            'overduePaymentCount' => $overduePaymentCount, 'overdueApprovalCount' => $overdueApprovalCount,
        ]);
    }
}
