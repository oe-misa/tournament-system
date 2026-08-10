<?php

use App\Models\AuditLog;
use App\Models\Membership;
use App\Models\User;

it('allows an admin to filter annual registrations and view audit logs without personal values', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['name' => '対象会員']);
    Membership::create(['user_id' => $member->id, 'start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => Membership::STATUS_PENDING_PAYMENT, 'payment_reference' => 'FURIKOMI-001']);
    AuditLog::create(['actor_id' => $admin->id, 'event' => 'updated', 'auditable_type' => User::class, 'auditable_id' => $member->id, 'changed_fields' => ['account_status']]);

    $this->actingAs($admin)->get('/admin/memberships?'.http_build_query(['q' => '対象会員', 'payment_reference' => 'FURIKOMI']))->assertOk()->assertSee('対象会員');
    $this->actingAs($admin)->get('/admin/audit-logs?q='.$admin->email)->assertOk()->assertSee('account_status')->assertDontSee('password');
});

it('shows annual membership reports as csv and print views only to admins', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create(['name' => '報告対象', 'email' => 'report@example.test']);
    $membership = Membership::create(['user_id' => $member->id, 'start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => Membership::STATUS_APPROVED, 'payment_reference' => 'REF-42']);
    $membership->update(['payment_confirmed_at' => now(), 'payment_confirmed_on' => today(), 'payment_confirmed_by' => $admin->id, 'approved_at' => now(), 'approved_by' => $admin->id, 'admin_comment' => '確認済み']);

    $this->actingAs($admin)->get('/admin/memberships/report?year=2026&q=report%40example.test')->assertOk()->assertSee('報告対象')->assertSee('REF-42');
    $this->actingAs($admin)->get('/admin/memberships/report.csv?year=2026')->assertOk()->assertDownload('annual-memberships-2026.csv');
    $this->actingAs($admin)->get('/admin/memberships/report/print?year=2026')->assertOk()->assertSee('印刷');
    $this->actingAs($member)->get('/admin/memberships/report')->assertForbidden();
});

it('emphasizes and filters overdue annual membership work', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $member = User::factory()->create();
    $pending = Membership::create(['user_id' => $member->id, 'start_date' => '2026-04-01', 'end_date' => '2027-03-31', 'status' => Membership::STATUS_PENDING_PAYMENT]);
    $pending->forceFill(['created_at' => now()->subDays(7)])->saveQuietly();
    $confirmed = Membership::create(['user_id' => $member->id, 'start_date' => '2027-04-01', 'end_date' => '2028-03-31', 'status' => Membership::STATUS_PAYMENT_CONFIRMED, 'payment_confirmed_at' => now()->subDays(3)]);

    $this->actingAs($admin)->get('/admin/memberships?scope=overdue_payment')->assertOk()->assertSee('申請から7日超過');
    $this->actingAs($admin)->get('/admin/memberships?scope=overdue_approval')->assertOk()->assertSee('確認から3日超過');
});
