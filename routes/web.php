<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Web\TournamentController;
use App\Http\Controllers\Web\EntryController;
use App\Http\Controllers\Web\ResultController;
use App\Http\Controllers\Web\RankRequestController;
use App\Http\Controllers\Web\MembershipController;
use App\Http\Controllers\Web\RankDefinitionController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminTournamentController;
use App\Http\Controllers\Admin\AdminRankRequestController;
use App\Http\Controllers\Admin\AdminResultController;

Route::get('/', function () {
    return view('welcome');
});

// 会員（authのみ）
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 会員：大会
    Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
    Route::get('/tournaments/{tournament}', [TournamentController::class, 'show'])->name('tournaments.show');

    // 会員：エントリー
    Route::post('/tournaments/{tournament}/entry', [EntryController::class, 'store'])->name('entries.store');

    // 会員：成績
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');

    // 会員：段位申請
    Route::get('/rank-requests', [RankRequestController::class, 'create'])->name('rank_requests.create');
    Route::post('/rank-requests', [RankRequestController::class, 'store'])->name('rank_requests.store');

    // ★会員：段位申請 履歴
    Route::get('/rank-requests/history', [RankRequestController::class, 'history'])->name('rank_requests.history');

    // 段位定義（選択時に取得してプレビューに使う）
    Route::get('/rank-definitions/{rank}', [RankDefinitionController::class, 'show'])->name('rank_definitions.show');

    // 会員：年間登録更新
    Route::get('/membership/renew', [MembershipController::class, 'create'])->name('membership.create');
    Route::post('/membership/renew', [MembershipController::class, 'store'])->name('membership.store');
});

// 管理者（auth + admin）
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 大会 CRUD
    Route::resource('tournaments', AdminTournamentController::class);

    // 段位申請 管理（履歴含む）
    Route::get('rank-requests', [AdminRankRequestController::class, 'index'])->name('rank_requests.index');
    Route::post('rank-requests/{rankRequest}/approve', [AdminRankRequestController::class, 'approve'])->name('rank_requests.approve');
    Route::post('rank-requests/{rankRequest}/reject', [AdminRankRequestController::class, 'reject'])->name('rank_requests.reject');

    // 成績入力（大会ごと）
    Route::get('tournaments/{tournament}/results', [AdminResultController::class, 'edit'])->name('results.edit');
    Route::post('tournaments/{tournament}/results', [AdminResultController::class, 'update'])->name('results.update');
});

require __DIR__ . '/auth.php';
