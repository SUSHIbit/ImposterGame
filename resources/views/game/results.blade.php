<!-- resources/views/game/results.blade.php -->
@extends('layouts.game')

@section('game-nav')
<a href="{{ route('home') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1 rounded text-sm">
    Back to Home
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-6">
        <div class="bg-indigo-800 px-6 py-4">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-white">Game Results</h2>
                <div class="bg-indigo-900 text-white px-3 py-1 rounded-full text-sm">
                    Room: {{ $room->code }}
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-gray-700 rounded-lg p-6 mb-6">
                <h3 class="text-xl font-semibold text-white mb-4">Final Leaderboard</h3>
                
                <div class="overflow-hidden rounded-lg mb-6">
                    <table class="min-w-full bg-gray-800">
                        <thead>
                            <tr>
                                <th class="py-3 px-4 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rank</th>
                                <th class="py-3 px-4 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Player</th>
                                <th class="py-3 px-4 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Score</th>
                                <th class="py-3 px-4 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Times as Imposter</th>
                                <th class="py-3 px-4 bg-gray-900 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Votes Received</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($players as $index => $player)
                                <tr class="{{ $index === 0 ? 'bg-indigo-900' : ($index % 2 === 0 ? 'bg-gray-800' : 'bg-gray-850') }}">
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="text-white">
                                            {{ $index + 1 }}
                                            @if($index === 0)
                                                <span class="ml-1">🏆</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-indigo-800 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">
                                                {{ substr($player->user->name, 0, 1) }}
                                            </div>
                                            <div class="font-medium text-white">{{ $player->user->name }}</div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="text-white font-semibold">{{ $player->score }}</div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="text-white">
                                            {{ $rounds->where('imposter_user_id', $player->user_id)->count() }}
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="text-white">
                                            @php
                                                $votesReceived = 0;
                                                foreach ($rounds as $round) {
                                                    $votesReceived += $round->votes->where('guessed_user_id', $player->user_id)->count();
                                                }
                                            @endphp
                                            {{ $votesReceived }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="bg-indigo-900 p-4 rounded-lg text-center">
                    <p class="text-white font-semibold text-lg">Congratulations to {{ $players->first()->user->name }} for winning the game!</p>
                </div>
            </div>
            
            <div class="bg-gray-700 rounded-lg p-6">
                <h3 class="text-xl font-semibold text-white mb-4">Round History</h3>
                
                <div class="space-y-6">
                    @foreach($rounds as $round)
                        <div class="bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-white mb-2">Round {{ $round->round_number }}</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="bg-gray-900 p-3 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-300 mb-1">Normal Question:</h5>
                                    <p class="text-white">{{ $round->questionSet->getNormalQuestion()->content }}</p>
                                </div>
                                
                                <div class="bg-gray-900 p-3 rounded-lg">
                                    <h5 class="text-sm font-medium text-gray-300 mb-1">Imposter Question:</h5>
                                    <p class="text-white">{{ $round->questionSet->getImposterQuestion()->content }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center px-3 py-2 bg-indigo-800 rounded-lg mb-4">
                                <div class="font-medium text-white mr-2">Imposter:</div>
                                <div class="flex items-center">
                                    <div class="bg-indigo-700 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-2">
                                        {{ substr($round->imposter->name, 0, 1) }}
                                    </div>
                                    <div class="text-white">{{ $round->imposter->name }}</div>
                                </div>
                                <div class="ml-auto text-indigo-200 text-sm">
                                    Votes received: {{ $round->votes->where('guessed_user_id', $round->imposter_user_id)->count() }}
                                </div>
                            </div>
                            
                            <h5 class="text-sm font-medium text-gray-300 mb-1">Player Answers:</h5>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4">
                                @foreach($round->answers as $answer)
                                    <div class="bg-gray-900 p-3 rounded-lg">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center">
                                                <div class="bg-indigo-800 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-2">
                                                    {{ substr($answer->user->name, 0, 1) }}
                                                </div>
                                                <div class="text-white text-sm">{{ $answer->user->name }}</div>
                                            </div>
                                            <div class="text-xs {{ $answer->user_id == $round->imposter_user_id ? 'text-red-400' : 'text-green-400' }}">
                                                {{ $answer->user_id == $round->imposter_user_id ? 'Imposter' : 'Normal' }}
                                            </div>
                                        </div>
                                        <p class="text-gray-300 text-sm">{{ $answer->answer_text }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-semibold">
                    Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
@endsection items-center">
                <h2 class="text-2xl font-bold text-white">Room: {{ $room->code }}</h2>
                <div class="flex space-x-4 items-center">
                    <div class="bg-indigo-900 text-white px-3 py-1 rounded-full text-sm">
                        Round {{ $round->round_number }} of 5
                    </div>
                    <div id="timer" class="bg-indigo-700 text-white px-3 py-1 rounded-full font-mono">
                        00:30
                    </div>
                </div>
            </div>
        </div>
        
        <div id="game-container" class="p-6">
            <div id="answering-phase" class="{{ $round->isAnswering() ? '' : 'hidden' }}">
                <div class="bg-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Your Question:</h3>
                    <div class="bg-gray-600 p-4 rounded-lg mb-4">
                        <p class="text-white text-lg">{{ $question->content }}</p>
                    </div>
                    
                    <form id="answer-form" action="{{ route('game.answer', $room->code) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="answer" class="block text-gray-300 font-medium mb-2">Your Answer:</label>
                            <textarea id="answer" name="answer" rows="3" 
                                      class="w-full px-4 py-2 bg-gray-900 text-white border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                      {{ $round->hasPlayerAnswered(Auth::id()) ? 'disabled' : 'required' }}
                                      placeholder="Type your answer here...">{{ $round->answers->where('user_id', Auth::id())->first()->answer_text ?? '' }}</textarea>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-semibold {{ $round->hasPlayerAnswered(Auth::id()) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $round->hasPlayerAnswered(Auth::id()) ? 'disabled' : '' }}>
                                {{ $round->hasPlayerAnswered(Auth::id()) ? 'Answer Submitted' : 'Submit Answer' }}
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="bg-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Players</h3>
                    <div id="players-status" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($room->players as $player)
                            <div class="bg-gray-800 shadow-sm rounded-lg p-3 flex items-center space-x-2 
                                        {{ $round->hasPlayerAnswered($player->user_id) ? 'border-green-500 border' : 'border-gray-600 border' }}">
                                <div class="bg-indigo-800 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold">
                                    {{ substr($player->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-white text-sm">{{ $player->user->name }}</div>
                                    <div class="text-xs {{ $round->hasPlayerAnswered($player->user_id) ? 'text-green-400' : 'text-gray-400' }}">
                                        {{ $round->hasPlayerAnswered($player->user_id) ? 'Answered' : 'Answering...' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div id="voting-phase" class="{{ $round->isVoting() ? '' : 'hidden' }}">
                <div class="bg-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">All Answers - Who is the Imposter?</h3>
                    
                    <form id="vote-form" action="{{ route('game.vote', $room->code) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($round->answers as $answer)
                                <div class="bg-gray-800 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="bg-indigo-800 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-2">
                                                {{ substr($answer->user->name, 0, 1) }}
                                            </div>
                                            <div class="font-medium text-white">{{ $answer->user->name }}</div>
                                        </div>
                                        
                                        @if($answer->user_id != Auth::id() && !$round->hasPlayerVoted(Auth::id()))
                                            <div>
                                                <input type="radio" 
                                                       id="vote_{{ $answer->user_id }}" 
                                                       name="user_id" 
                                                       value="{{ $answer->user_id }}" 
                                                       class="sr-only peer" 
                                                       required>
                                                <label for="vote_{{ $answer->user_id }}" 
                                                       class="cursor-pointer py-1 px-3 rounded-full text-sm
                                                             peer-checked:bg-indigo-600 peer-checked:text-white 
                                                             bg-gray-700 text-gray-300 hover:bg-gray-600">
                                                    Vote
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-gray-300 mt-2">
                                        <p>{{ $answer->answer_text }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if(!$round->hasPlayerVoted(Auth::id()))
                            <div class="flex justify-end">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-semibold">
                                    Submit Vote
                                </button>
                            </div>
                        @else
                            <div class="bg-indigo-900 text-white p-4 rounded-lg text-center">
                                <p class="font-medium">Vote submitted! Waiting for other players...</p>
                            </div>
                        @endif
                    </form>
                </div>
                
                <div class="bg-gray-700 rounded-lg p-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Voting Status</h3>
                    <div id="voting-status" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        @foreach($room->players as $player)
                            <div class="bg-gray-800 shadow-sm rounded-lg p-3 flex items-center space-x-2 
                                        {{ $round->hasPlayerVoted($player->user_id) ? 'border-green-500 border' : 'border-gray-600 border' }}">
                                <div class="bg-indigo-800 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold">
                                    {{ substr($player->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-white text-sm">{{ $player->user->name }}</div>
                                    <div class="text-xs {{ $round->hasPlayerVoted($player->user_id) ? 'text-green-400' : 'text-gray-400' }}">
                                        {{ $round->hasPlayerVoted($player->user_id) ? 'Voted' : 'Voting...' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div id="round-results" class="{{ $round->isCompleted() ? '' : 'hidden' }}">
                <div class="bg-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-xl font-semibold text-white mb-4">Round Results</h3>
                    
                    <div class="bg-indigo-900 p-4 rounded-lg mb-6">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-white">The Imposter was:</h4>
                            <div class="flex items-center">
                                <div class="bg-indigo-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-2">
                                    {{ substr($round->imposter->name, 0, 1) }}
                                </div>
                                <div class="font-medium text-white">{{ $round->imposter->name }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-white mb-2">Question for Normal Players:</h4>
                            <p class="text-gray-300">{{ $round->questionSet->getNormalQuestion()->content }}</p>
                        </div>
                        
                        <div class="bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-white mb-2">Question for Imposter:</h4>
                            <p class="text-gray-300">{{ $round->questionSet->getImposterQuestion()->content }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="font-semibold text-white mb-2">Voting Results:</h4>
                        <div class="bg-gray-800 p-4 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($room->players as $player)
                                    <div class="bg-gray-900 p-3 rounded-lg">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <div class="bg-indigo-800 text-white rounded-full w-6 h-6 flex items-center justify-center font-bold text-xs mr-2">
                                                    {{ substr($player->user->name, 0, 1) }}
                                                </div>
                                                <div class="font-medium text-white text-sm">{{ $player->user->name }}</div>
                                            </div>
                                            <div class="text-sm font-semibold {{ $player->user_id == $round->imposter_user_id ? 'text-red-400' : 'text-green-400' }}">
                                                {{ $player->user_id == $round->imposter_user_id ? 'Imposter' : 'Normal' }}
                                            </div>
                                        </div>
                                        <div class="mt-1 flex items-center justify-between">
                                            <span class="text-gray-400 text-sm">Votes received:</span>
                                            <span class="bg-indigo-900 text-white px-2 py-0.5 rounded-full text-xs">
                                                {{ $round->getVotesForUser($player->user_id) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        @if($round->round_number < 5)
                            <div class="bg-indigo-700 text-white p-4 rounded-lg">
                                <p class="font-semibold">Next round starting soon...</p>
                            </div>
                        @else
                            <a href="{{ route('game.results', $room->code) }}" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-semibold">
                                View Game Results
                            </a>
                        @endif
                    </div>
                </div>
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
        const roundId = '{{ $round->id }}';
        const roundNumber = {{ $round->round_number }};
        
        let timeLeft = 30; // 30 seconds for each phase
        let timerInterval;
        
        function startTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);
        }
        
        function updateTimer() {
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                // Auto-submit current phase or move to next phase
                autoSubmitCurrentPhase();
            } else {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('timer').textContent = 
                    (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }
        }
        
        function autoSubmitCurrentPhase() {
            const answeringPhase = document.getElementById('answering-phase');
            const votingPhase = document.getElementById('voting-phase');
            
            if (answeringPhase && !answeringPhase.classList.contains('hidden')) {
                document.getElementById('answer-form').submit();
            } else if (votingPhase && !votingPhase.classList.contains('hidden')) {
                // Select a random vote if none selected
                const voteForm = document.getElementById('vote-form');
                const radioButtons = voteForm.querySelectorAll('input[type="radio"]');
                
                if (radioButtons.length > 0) {
                    const randomIndex = Math.floor(Math.random() * radioButtons.length);
                    radioButtons[randomIndex].checked = true;
                    voteForm.submit();
                }
            }
        }
        
        // Set up real-time subscription
        const channel = supabase
            .channel(`round:${roundId}`)
            .on('*', (payload) => {
                console.log('Real-time update:', payload);
                
                // Handle different events
                switch(payload.event) {
                    case 'answer_submitted':
                        updatePlayerAnswerStatus(payload.payload);
                        break;
                    case 'voting_started':
                        showVotingPhase();
                        break;
                    case 'vote_submitted':
                        updatePlayerVoteStatus(payload.payload);
                        break;
                    case 'round_completed':
                        showRoundResults(payload.payload);
                        break;
                    case 'game_completed':
                        window.location.href = "{{ route('game.results', $room->code) }}";
                        break;
                }
            })
            .subscribe();
            
        function updatePlayerAnswerStatus(data) {
            const userId = data.user_id;
            const playersStatus = document.getElementById('players-status');
            const playerDivs = playersStatus.querySelectorAll('div.bg-gray-800');
            
            playerDivs.forEach(div => {
                if (div.dataset.userId == userId) {
                    div.classList.remove('border-gray-600');
                    div.classList.add('border-green-500');
                    
                    const statusDiv = div.querySelector('div.text-xs');
                    statusDiv.textContent = 'Answered';
                    statusDiv.classList.remove('text-gray-400');
                    statusDiv.classList.add('text-green-400');
                }
            });
            
            // Check if all players have answered
            const allAnswered = Array.from(playerDivs).every(div => div.classList.contains('border-green-500'));
            if (allAnswered) {
                showVotingPhase();
            }
        }
        
        function showVotingPhase() {
            document.getElementById('answering-phase').classList.add('hidden');
            document.getElementById('voting-phase').classList.remove('hidden');
            document.getElementById('round-results').classList.add('hidden');
            
            // Reset timer for voting phase
            timeLeft = 30;
            startTimer();
        }
        
        function updatePlayerVoteStatus(data) {
            const userId = data.user_id;
            const votingStatus = document.getElementById('voting-status');
            const playerDivs = votingStatus.querySelectorAll('div.bg-gray-800');
            
            playerDivs.forEach(div => {
                if (div.dataset.userId == userId) {
                    div.classList.remove('border-gray-600');
                    div.classList.add('border-green-500');
                    
                    const statusDiv = div.querySelector('div.text-xs');
                    statusDiv.textContent = 'Voted';
                    statusDiv.classList.remove('text-gray-400');
                    statusDiv.classList.add('text-green-400');
                }
            });
            
            // Check if all players have voted
            const allVoted = Array.from(playerDivs).every(div => div.classList.contains('border-green-500'));
            if (allVoted) {
                setTimeout(() => {
                    showRoundResults();
                }, 2000); // 2-second delay before showing results
            }
        }
        
        function showRoundResults(data) {
            document.getElementById('answering-phase').classList.add('hidden');
            document.getElementById('voting-phase').classList.add('hidden');
            document.getElementById('round-results').classList.remove('hidden');
            
            // Clear any running timers
            clearInterval(timerInterval);
            
            // If it's the last round, show game completed message
            if (roundNumber >= 5) {
                setTimeout(() => {
                    window.location.href = "{{ route('game.results', $room->code) }}";
                }, 10000); // 10-second delay before redirecting to final results
            } else {
                // Wait for next round to start
                setTimeout(() => {
                    window.location.reload();
                }, 5000); // 5-second delay before reloading for next round
            }
        }
        
        // Initialize the timer
        startTimer();
    });
</script>
@endpush
@endsection