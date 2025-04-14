<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GameController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Game API endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/rooms/{code}', [RoomController::class, 'getRoom']);
    Route::get('/rooms/{code}/players', [RoomController::class, 'getPlayers']);
    Route::get('/rounds/{roundId}', [GameController::class, 'getRound']);
    Route::get('/rounds/{roundId}/answers', [GameController::class, 'getAnswers']);
    Route::get('/rounds/{roundId}/votes', [GameController::class, 'getVotes']);
});