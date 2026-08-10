<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use App\Services\MembershipService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AdminMembershipController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->string('scope')->toString() ?: 'actionable';
        $query = $this->filteredQuery($request, $scope);

        return view('admin.memberships.index', ['memberships' => $query->paginate(30)->withQueryString(), 'scope' => $scope, 'admins' => \App\Models\User::where('is_admin', true)->orderBy('name')->get(['id','name'])]);
    }

    public function report(Request $request)
    {
        $year = (int) ($request->query('year') ?: now()->year);
        return view('admin.memberships.report', ['memberships' => $this->filteredQuery($request)->whereYear('start_date', $year)->get(), 'year' => $year]);
    }

    public function export(Request $request)
    {
        $year = (int) ($request->query('year') ?: now()->year);
        $memberships = $this->filteredQuery($request)->whereYear('start_date', $year)->get();
        return response()->streamDownload(function () use ($memberships) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['年度', '会員名', 'メール', '申請状態', '振込照合情報', '確認日', '確認者', '承認日', '承認者', 'コメント']);
            foreach ($memberships as $membership) fputcsv($out, [$membership->start_date->year, $membership->user->name, $membership->user->email, $membership->status, $membership->payment_reference, optional($membership->payment_confirmed_on)->format('Y-m-d'), $membership->paymentConfirmer?->name, optional($membership->approved_at)->format('Y-m-d'), $membership->approver?->name, $membership->admin_comment]);
            fclose($out);
        }, "annual-memberships-{$year}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function print(Request $request)
    {
        $year = (int) ($request->query('year') ?: now()->year);
        return view('admin.memberships.print', ['memberships' => $this->filteredQuery($request)->whereYear('start_date', $year)->get(), 'year' => $year]);
    }

    private function filteredQuery(Request $request, ?string $scope = null)
    {
        $query = Membership::query()->with(['user:id,name,email', 'paymentConfirmer:id,name', 'approver:id,name', 'rejector:id,name'])->latest();
        if ($scope === 'actionable') $query->whereIn('status', [Membership::STATUS_PENDING_PAYMENT, Membership::STATUS_PAYMENT_CONFIRMED]);
        elseif ($scope === 'overdue_payment') $query->where('status', Membership::STATUS_PENDING_PAYMENT)->where('created_at', '<=', now()->subDays(7));
        elseif ($scope === 'overdue_approval') $query->where('status', Membership::STATUS_PAYMENT_CONFIRMED)->where('payment_confirmed_at', '<=', now()->subDays(3));
        elseif (in_array($scope, [Membership::STATUS_PENDING_PAYMENT, Membership::STATUS_PAYMENT_CONFIRMED, Membership::STATUS_APPROVED, Membership::STATUS_REJECTED], true)) $query->where('status', $scope);
        if ($q = trim((string) $request->query('q'))) $query->whereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
        foreach (['status', 'payment_reference'] as $field) if ($value = $request->query($field)) $query->where($field, 'like', "%{$value}%");
        if ($year = $request->query('year')) $query->whereYear('start_date', $year);
        if ($comment = $request->query('comment')) $query->where('admin_comment', 'like', "%{$comment}%");
        if ($confirmer = $request->query('payment_confirmed_by')) $query->where('payment_confirmed_by', $confirmer);
        if ($approver = $request->query('approved_by')) $query->where('approved_by', $approver);
        return $query;
    }

    public function confirmPayment(Request $request, Membership $membership, MembershipService $service)
    {
        $data = $request->validate(['admin_comment' => ['nullable', 'string', 'max:2000'], 'payment_reference' => ['nullable', 'string', 'max:255'], 'payment_confirmed_on' => ['nullable', 'date']]);
        try {
            $service->confirmPayment($request->user(), $membership, $data['admin_comment'] ?? null, $data['payment_reference'] ?? null, $data['payment_confirmed_on'] ?? null);
            return back()->with('status', '入金を確認しました。承認待ちです。');
        } catch (HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, Membership $membership, MembershipService $service)
    {
        $data = $request->validate(['admin_comment' => ['nullable', 'string', 'max:2000']]);
        try {
            $service->approve($request->user(), $membership, $data['admin_comment'] ?? null);
            return back()->with('status', '年間登録を承認し、有効期限を更新しました。');
        } catch (HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Membership $membership, MembershipService $service)
    {
        $data = $request->validate(['admin_comment' => ['nullable', 'string', 'max:2000']]);
        try {
            $service->reject($request->user(), $membership, $data['admin_comment'] ?? null);
            return back()->with('status', '年間登録申請を却下しました。');
        } catch (HttpException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
