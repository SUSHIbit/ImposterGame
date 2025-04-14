<?php

// app/Services/GameService.php
namespace App\Services;

use App\Models\Room;
use App\Models\Round;
use App\Models\QuestionSet;
use App\Models\RoomPlayer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GameService
{
    protected $supabaseService;
    
    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }
    
    public function startNewRound(Room $room)
    {
        // Get random question set
        $questionSet = QuestionSet::inRandomOrder()->first();
        
        if (!$questionSet) {
            throw new \Exception('No question sets available.');
        }
        
        // Get all players in the room
        $playerIds = $room->players()->pluck('user_id')->toArray();
        
        if (empty($playerIds)) {
            throw new \Exception('No players in the room.');
        }
        
        // Randomly select imposter
        $imposterIndex = array_rand($playerIds);
        $imposterUserId = $playerIds[$imposterIndex];
        
        // Create new round
        $round = Round::create([
            'room_id' => $room->id,
            'round_number' => $room->current_round,
            'question_set_id' => $questionSet->id,
            'imposter_user_id' => $imposterUserId,
            'status' => 'answering',
            'started_at' => now(),
        ]);
        
        // Broadcast round started event
        $this->supabaseService->broadcastToRoom($room->code, 'round_started', [
            'room_id' => $room->id,
            'round_number' => $round->round_number,
        ]);
        
        return $round;
    }
    
    public function getQuestionForUser(Round $round, $userId)
    {
        $isImposter = $round->isImposter($userId);
        
        if ($isImposter) {
            return $round->questionSet->getImposterQuestion();
        } else {
            return $round->questionSet->getNormalQuestion();
        }
    }
    
    public function startVotingPhase(Round $round)
    {
        $round->update([
            'status' => 'voting',
        ]);
        
        // Broadcast voting started event
        $room = $round->room;
        $this->supabaseService->broadcastToRoom($room->code, 'voting_started', [
            'room_id' => $room->id,
            'round_number' => $round->round_number,
        ]);
        
        return $round;
    }
    
    public function completeRound(Round $round)
    {
        $round->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Calculate scores
        $imposterUserId = $round->imposter_user_id;
        $votesForImposter = $round->getVotesForUser($imposterUserId);
        
        // Get all players in the room
        $roomPlayers = $round->room->players;
        $totalPlayers = $roomPlayers->count();
        
        // If no one voted for the imposter, imposter gets points
        if ($votesForImposter === 0) {
            $imposterPlayer = $roomPlayers->firstWhere('user_id', $imposterUserId);
            if ($imposterPlayer) {
                $imposterPlayer->increment('score', 2);
            }
        } else {
            // Each player who correctly identified the imposter gets points
            foreach ($round->votes as $vote) {
                if ($vote->guessed_user_id === $imposterUserId) {
                    $player = $roomPlayers->firstWhere('user_id', $vote->voter_id);
                    if ($player) {
                        $player->increment('score');
                    }
                }
            }
        }
        
        // Broadcast round completed event
        $room = $round->room;
        $this->supabaseService->broadcastToRoom($room->code, 'round_completed', [
            'room_id' => $room->id,
            'round_number' => $round->round_number,
            'imposter_user_id' => $imposterUserId,
            'votes_for_imposter' => $votesForImposter,
        ]);
        
        return $round;
    }
    
    public function startNextRound(Room $room)
    {
        $nextRoundNumber = $room->current_round + 1;
        
        if ($nextRoundNumber > 5) {
            return $this->completeGame($room);
        }
        
        $room->update([
            'current_round' => $nextRoundNumber,
        ]);
        
        return $this->startNewRound($room);
    }
    
    public function completeGame(Room $room)
    {
        $room->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Calculate final scores and determine winner
        $players = $room->players()->with('user')->get()->sortByDesc('score');
        $winner = $players->first();
        
        // Broadcast game completed event
        $this->supabaseService->broadcastToRoom($room->code, 'game_completed', [
            'room_id' => $room->id,
            'winner_id' => $winner ? $winner->user_id : null,
            'players' => $players->map(function ($player) {
                return [
                    'user_id' => $player->user_id,
                    'name' => $player->user->name,
                    'score' => $player->score,
                ];
            }),
        ]);
        
        return $room;
    }
}