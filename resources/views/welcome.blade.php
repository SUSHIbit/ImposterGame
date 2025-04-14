<!-- resources/views/welcome.blade.php -->
@extends('layouts.app')

@section('content')
<div class="text-center">
    <h1 class="text-4xl font-bold text-indigo-700 mb-4">Welcome to Imposter Game</h1>
    <p class="text-lg text-gray-600 mb-8">Can you find the imposter or blend in undetected?</p>
    
    <div class="max-w-lg mx-auto">
        <div class="bg-indigo-100 p-8 rounded-lg shadow-lg mb-8 flex items-center justify-center">
            <h2 class="text-3xl font-bold text-indigo-800">Imposter Game</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">How to Play</h2>
                <p class="text-gray-600">
                    Join a room with friends and try to blend in or catch the imposter! Each round, one player gets a
                    different question than everyone else. Can you spot the odd one out?
                </p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Game Rules</h2>
                <ul class="text-gray-600 list-disc list-inside">
                    <li>Each game has 5 rounds</li>
                    <li>One player is the imposter each round</li>
                    <li>Players answer questions and vote</li>
                    <li>Stay undetected or catch the imposter to win!</li>
                </ul>
            </div>
        </div>
        
        <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 justify-center">
            @auth
                <a href="{{ route('room.create') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-lg shadow-md">
                    Create a Room
                </a>
                <a href="{{ route('room.join') }}" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-6 rounded-lg shadow-md">
                    Join a Room
                </a>
            @else
                <a href="{{ route('login') }}" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-lg shadow-md">
                    Login to Play
                </a>
                <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-500 text-white font-bold py-3 px-6 rounded-lg shadow-md">
                    Register Now
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection