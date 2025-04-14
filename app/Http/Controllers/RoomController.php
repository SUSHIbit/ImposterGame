<?php

// app/Http/Controllers/RoomController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomPlayer;
use App\Services\RoomService;
use App\Http\Requests\CreateRoomRequest;
use App\Http\Requests\JoinRoomRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseService;

class RoomController extends Controller
{
    protected $roomService;
    protected $supabaseService;
    
    public function __construct(RoomService $roomService, SupabaseService $supabaseService)
    {
        $this->middleware('auth');
        $this->roomService = $roomService;
        $this->supabaseService = $supabaseService;
    }
    
    public function create()
    {
        return view('room.create');
    }
    
    public function store(CreateRoomRequest $request)
    {
        $user = Auth::user();
        $room = $this->roomService->createRoom($user, $request->validated());
        
        return redirect()->route('room.lobby', $room->code);
    }
    
    public function join()
    {
        return view('room.join');
    }
    
    public function joinRoom(JoinRoomRequest $request)
    {
        $validated = $request->validated();
        $code = strtoupper($validated['code']);
        $user = Auth::user();
        
        $room = Room::where('code', $code)->first();
        
        if (!$room) {
            return redirect()->back()->with('error', 'Room not found.');
        }
        
        if (!$room->canJoin()) {
            return redirect()->back()->with('error', 'Cannot join this room.');
        }
        
        // Check if user is already in the room
        $existingPlayer = RoomPlayer::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$existingPlayer) {
            // Add player to room
            RoomPlayer::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
            ]);
            
            // Broadcast player joined event
            $this->supabaseService->broadcastToRoom($room->code, 'player_joined', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ]
            ]);
        }
        
        return redirect()->route('room.lobby', $room->code);
    }
    
    public function lobby($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        
        // Check if user is in the room
        $player = RoomPlayer::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$player) {
            return redirect()->route('room.join')->with('error', 'You are not in this room.');
        }
        
        return view('room.lobby', compact('room', 'player'));
    }
    
    public function setReady(Request $request, $code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        
        $player = RoomPlayer::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->firstOrFail();
            
        $player->update(['is_ready' => !$player->is_ready]);
        
        // Broadcast player ready status
        $this->supabaseService->broadcastToRoom($room->code, 'player_ready', [
            'user_id' => $user->id,
            'is_ready' => $player->is_ready
        ]);
        
        return response()->json(['success' => true, 'is_ready' => $player->is_ready]);
    }
    
    public function startGame($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        
        // Check if user is the host
        if ($room->host_user_id !== $user->id) {
            return redirect()->back()->with('error', 'Only the host can start the game.');
        }
        
        // Check if room can start
        if (!$room->canStart()) {
            return redirect()->back()->with('error', 'Cannot start game. Minimum players not reached.');
        }
        
        // Start game
        $this->roomService->startGame($room);
        
        return redirect()->route('game.play', $room->code);
    }
    
    public function leaveRoom($code)
    {
        $room = Room::where('code', $code)->firstOrFail();
        $user = Auth::user();
        
        $player = RoomPlayer::where('room_id', $room->id)
            ->where('user_id', $user->id)
            ->first();
            
        if ($player) {
            $player->delete();
            
            // Broadcast player left event
            $this->supabaseService->broadcastToRoom($room->code, 'player_left', [
                'user_id' => $user->id
            ]);
            
            // If user is host and room is waiting, delete room
            if ($room->host_user_id === $user->id && $room->isWaiting()) {
                $room->delete();
            }
        }
        
        return redirect()->route('home');
    }
}