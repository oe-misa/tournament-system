<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\TournamentController;
use App\Http\Controllers\Api\EntryController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\RankRequestController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [MeController::class, 'show']);

    Route::get('/tournaments', [TournamentController::class, 'index']);
    Route::get('/tournaments/{tournament}', [TournamentController::class, 'show']);

    Route::post('/tournaments/{tournament}/entries', [EntryController::class, 'store']);

    Route::get('/results', [ResultController::class, 'index']);

    Route::post('/rank-requests', [RankRequestController::class, 'store']);
});
