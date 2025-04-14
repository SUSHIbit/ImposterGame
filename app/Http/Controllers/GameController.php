<?php

// app/Http/Controllers/GameController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Round;
use App\Models\Answer;
use App\Models\Vote;
use App\Models\RoomPlayer;
use App\Services\GameService;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseService;

class GameController extends Controller
{
    protected $gameService;
    protected $supabaseService;
    
    public function __construct(GameService $gameService, SupabaseService $supabaseService)
    {
        $this->middleware('auth');
        $this->gameService = $gameService;
        $this->supabaseService = $supabaseService;
    }
    
    public function play($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        
        // Check if user is in the room
        $player = RoomPlayer::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$player) {
            return redirect()->route('home')->with('error', 'You are not part of this game.');
        }
        
        // Check if game is in progress
        if (!$room->isInProgress()) {
            return redirect()->route('room.lobby', $room->code);
        }
        
        $round = $room->currentRound();
        
        if (!$round) {
            return redirect()->route('room.lobby', $room->code);
        }
        
        // Get the appropriate question for the user
        $question = $this->gameService->getQuestionForUser($round, $user->id);
        
        return view('game.play', compact('room', 'round', 'question'));
    }
    
    public function submitAnswer(Request $request, $code)
    {
        $request->validate([
            'answer' => 'required|string|max:500',
        ]);
        
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        $round = $room->currentRound();
        
        if (!$round || !$round->isAnswering()) {
            return response()->json(['error' => 'Cannot submit answer at this time.'], 400);
        }
        
        // Check if user already submitted an answer
        if ($round->hasPlayerAnswered($user->id)) {
            return response()->json(['error' => 'You have already submitted an answer.'], 400);
        }
        
        // Submit answer
        Answer::create([
            'round_id' => $round->id,
            'user_id' => $user->id,
            'answer_text' => $request->answer,
        ]);
        
        // Broadcast answer submitted event
        $this->supabaseService->broadcastToRoom($room->code, 'answer_submitted', [
            'user_id' => $user->id,
            'round_number' => $round->round_number
        ]);
        
        // Check if all players have answered
        $players = $room->players()->count();
        $answers = $round->answers()->count();
        
        if ($answers >= $players) {
            // Move to voting phase
            $this->gameService->startVotingPhase($round);
        }
        
        return response()->json(['success' => true]);
    }
    
    public function submitVote(Request $request, $code)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        $round = $room->currentRound();
        
        if (!$round || !$round->isVoting()) {
            return response()->json(['error' => 'Cannot vote at this time.'], 400);
        }
        
        // Check if user already voted
        if ($round->hasPlayerVoted($user->id)) {
            return response()->json(['error' => 'You have already voted.'], 400);
        }
        
        // Cannot vote for yourself
        if ($request->user_id == $user->id) {
            return response()->json(['error' => 'You cannot vote for yourself.'], 400);
        }
        
        // Submit vote
        Vote::create([
            'round_id' => $round->id,
            'voter_id' => $user->id,
            'guessed_user_id' => $request->user_id,
        ]);
        
        // Broadcast vote submitted event
        $this->supabaseService->broadcastToRoom($room->code, 'vote_submitted', [
            'user_id' => $user->id,
            'round_number' => $round->round_number
        ]);
        
        // Check if all players have voted
        $players = $room->players()->count();
        $votes = $round->votes()->count();
        
        if ($votes >= $players) {
            // Complete round and calculate scores
            $this->gameService->completeRound($round);
            
            // If this was the last round, complete the game
            if ($round->round_number == 5) {
                $this->gameService->completeGame($room);
            } else {
                // Start next round
                $this->gameService->startNextRound($room);
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    public function results($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        
        // Check if user is in the room
        $player = RoomPlayer::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$player) {
            return redirect()->route('home')->with('error', 'You are not part of this game.');
        }
        
        // Check if game is completed
        if (!$room->isCompleted()) {
            return redirect()->route('game.play', $room->code);
        }
        
        $rounds = $room->rounds()->with(['imposter', 'answers.user', 'votes', 'questionSet.questions'])->get();
        $players = $room->players()->with('user')->get()->sortByDesc('score');
        
        return view('game.results', compact('room', 'rounds', 'players'));
    }
}