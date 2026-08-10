<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Entry;
use App\Models\User;
use App\Jobs\SendMissingResultsReminder;
use App\Console\Commands\SendMembershipExpiryNotifications;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tournaments:remind-missing-results', function () {
    $count = Entry::query()->where('entries.status', 'entry')
        ->join('tournaments', 'tournaments.id', '=', 'entries.tournament_id')
        ->leftJoin('results', fn ($join) => $join->on('results.tournament_id', '=', 'entries.tournament_id')->on('results.user_id', '=', 'entries.user_id'))
        ->whereDate('tournaments.event_date', '<', today())->whereNull('results.id')->count();
    if ($count > 0) {
        User::query()->where('is_admin', true)->where('account_status', 'active')->each(fn ($admin) => SendMissingResultsReminder::dispatch($admin->id, $count));
    }
    $this->info("通知対象: {$count} 件");
})->purpose('Notify administrators about missing tournament results');

Schedule::command('tournaments:remind-missing-results')->dailyAt('09:00')->withoutOverlapping();
Schedule::command('memberships:send-expiry-notifications')->dailyAt('09:15')->withoutOverlapping();
