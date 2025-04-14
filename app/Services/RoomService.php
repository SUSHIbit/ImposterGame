<?php
// app/Services/RoomService.php
namespace App\Services;

use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\User;
use Illuminate\Support\Str;

class RoomService
{
    protected $supabaseService;
    
    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }
    
    public function createRoom(User $user, array $data = [])
    {
        $code = $this->generateUniqueCode();
        
        $room = Room::create([
            'code' => $code,
            'host_user_id' => $user->id,
            'min_players' => $data['min_players'] ?? 4,
            'max_players' => $data['max_players'] ?? 10,
        ]);
        
        // Add host as a player
        RoomPlayer::create([
            'room_id' => $room->id,
            'user_id' => $user->id,
            'is_ready' => true,
        ]);
        
        return $room;
    }
    
    public function startGame(Room $room)
    {
        if (!$room->canStart()) {
            throw new \Exception('Cannot start game. Minimum players not reached.');
        }
        
        $room->update([
            'status' => 'in_progress',
            'current_round' => 1,
            'started_at' => now(),
        ]);
        
        // Start first round
        app(GameService::class)->startNewRound($room);
        
        // Broadcast game started event
        $this->supabaseService->broadcastToRoom($room->code, 'game_started', [
            'room_id' => $room->id,
        ]);
        
        return $room;
    }
    
    private function generateUniqueCode()
    {
        $attempts = 0;
        do {
            $code = strtoupper(Str::random(5));
            $exists = Room::where('code', $code)->exists();
            $attempts++;
            
            if ($attempts > 10) {
                throw new \Exception('Failed to generate unique room code.');
            }
        } while ($exists);
        
        return $code;
    }
}