<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::get('/auth/callback', [AuthController::class, 'handleCallback'])->name('auth.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Room management
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('room.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('room.store');
    Route::get('/rooms/join', [RoomController::class, 'join'])->name('room.join');
    Route::post('/rooms/join', [RoomController::class, 'joinRoom'])->name('room.join.submit');
    Route::get('/rooms/{code}/lobby', [RoomController::class, 'lobby'])->name('room.lobby');
    Route::post('/rooms/{code}/ready', [RoomController::class, 'setReady'])->name('room.ready');
    Route::post('/rooms/{code}/start', [RoomController::class, 'startGame'])->name('room.start');
    Route::post('/rooms/{code}/leave', [RoomController::class, 'leaveRoom'])->name('room.leave');
    
    // Game play
    Route::get('/games/{code}/play', [GameController::class, 'play'])->name('game.play');
    Route::post('/games/{code}/answer', [GameController::class, 'submitAnswer'])->name('game.answer');
    Route::post('/games/{code}/vote', [GameController::class, 'submitVote'])->name('game.vote');
    Route::get('/games/{code}/results', [GameController::class, 'results'])->name('game.results');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/question-sets', [AdminController::class, 'questionSets'])->name('admin.question-sets');
    Route::post('/question-sets', [AdminController::class, 'createQuestionSet'])->name('admin.question-sets.store');
    Route::delete('/question-sets/{id}', [AdminController::class, 'deleteQuestionSet'])->name('admin.question-sets.delete');
});