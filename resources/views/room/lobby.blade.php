<!-- resources/views/room/lobby.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-indigo-600 px-6 py-4">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Room: {{ $room->code }}</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-indigo-100">
                        Players: {{ $room->players->count() }}/{{ $room->max_players }}
                    </span>
                    <form action="{{ route('room.leave', $room->code) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-400 text-white px-3 py-1 rounded">
                            Leave
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <div class="md:col-span-2">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Players</h3>
                    
                    <div id="players-list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($room->players as $roomPlayer)
                            <div class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between {{ $roomPlayer->user_id == $room->host_user_id ? 'border-2 border-indigo-500' : '' }}">
                                <div class="flex items-center">
                                    <div class="bg-indigo-100 text-indigo-800 rounded-full w-10 h-10 flex items-center justify-center font-bold mr-3">
                                        {{ substr($roomPlayer->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium">{{ $roomPlayer->user->name }}</div>
                                        <div class="text-xs text-gray-500">
                                            @if($roomPlayer->user_id == $room->host_user_id)
                                                Host
                                            @else
                                                Player
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="player-status">
                                    @if($roomPlayer->is_ready)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Ready
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Not Ready
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="md:col-span-1">
                <div class="bg-gray-50 rounded-lg p-6 h-full flex flex-col">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Game Settings</h3>
                    
                    <div class="space-y-3 text-sm mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Minimum Players:</span>
                            <span class="font-semibold">{{ $room->min_players }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Maximum Players:</span>
                            <span class="font-semibold">{{ $room->max_players }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Host:</span>
                            <span class="font-semibold">{{ $room->host->name }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-auto space-y-3">
                        <form action="{{ route('room.ready', $room->code) }}" method="POST" id="ready-form">
                            @csrf
                            <button type="submit" class="w-full {{ $player->is_ready ? 'bg-yellow-500 hover:bg-yellow-400' : 'bg-green-600 hover:bg-green-500' }} text-white py-2 px-4 rounded-lg font-semibold">
                                {{ $player->is_ready ? 'Not Ready' : 'Ready' }}
                            </button>
                        </form>
                        
                        @if($room->host_user_id == Auth::id())
                            <form action="{{ route('room.start', $room->code) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-2 px-4 rounded-lg font-semibold {{ $room->canStart() ? '' : 'opacity-50 cursor-not-allowed' }}"
                                        {{ $room->canStart() ? '' : 'disabled' }}>
                                    Start Game
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-100 px-6 py-4 border-t">
            <div class="text-center">
                <p class="text-gray-600">Share this room code with your friends:</p>
                <div class="text-2xl font-bold text-indigo-600 mt-1">{{ $room->code }}</div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supabaseUrl = '{{ config('supabase.url') }}';
        const supabaseKey = '{{ config('supabase.key') }}';
        const supabase = supabase.createClient(supabaseUrl, supabaseKey);
        
        const roomCode = '{{ $room->code }}';
        
        // Set up real-time subscription
        const channel = supabase
            .channel(`room:${roomCode}`)
            .on('*', (payload) => {
                console.log('Real-time update:', payload);
                
                // Handle different events
                switch(payload.event) {
                    case 'player_joined':
                        window.location.reload();
                        break;
                    case 'player_left':
                        window.location.reload();
                        break;
                    case 'player_ready':
                        updatePlayerReadyStatus(payload.payload);
                        break;
                    case 'game_started':
                        window.location.href = "{{ route('game.play', $room->code) }}";
                        break;
                }
            })
            .subscribe();
            
        // Handle ready form submission via AJAX
        const readyForm = document.getElementById('ready-form');
        if (readyForm) {
            readyForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                fetch(readyForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const button = readyForm.querySelector('button');
                        if (data.is_ready) {
                            button.textContent = 'Not Ready';
                            button.classList.remove('bg-green-600', 'hover:bg-green-500');
                            button.classList.add('bg-yellow-500', 'hover:bg-yellow-400');
                        } else {
                            button.textContent = 'Ready';
                            button.classList.remove('bg-yellow-500', 'hover:bg-yellow-400');
                            button.classList.add('bg-green-600', 'hover:bg-green-500');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
        
        function updatePlayerReadyStatus(data) {
            const playersList = document.getElementById('players-list');
            const playerDivs = playersList.querySelectorAll('div.bg-white');
            
            playerDivs.forEach(div => {
                const userId = div.dataset.userId;
                if (userId == data.user_id) {
                    const statusSpan = div.querySelector('.player-status span');
                    if (data.is_ready) {
                        statusSpan.textContent = 'Ready';
                        statusSpan.classList.remove('bg-yellow-100', 'text-yellow-800');
                        statusSpan.classList.add('bg-green-100', 'text-green-800');
                    } else {
                        statusSpan.textContent = 'Not Ready';
                        statusSpan.classList.remove('bg-green-100', 'text-green-800');
                        statusSpan.classList.add('bg-yellow-100', 'text-yellow-800');
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection