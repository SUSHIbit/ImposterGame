<!-- resources/views/room/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg p-6">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create a Game Room</h2>
    
    <form action="{{ route('room.store') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label for="min_players" class="block text-gray-700 font-medium mb-2">Minimum Players</label>
            <select id="min_players" name="min_players" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @for ($i = 3; $i <= 8; $i++)
                    <option value="{{ $i }}" {{ old('min_players', 4) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            @error('min_players')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="max_players" class="block text-gray-700 font-medium mb-2">Maximum Players</label>
            <select id="max_players" name="max_players" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @for ($i = 4; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ old('max_players', 10) == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            @error('max_players')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Create Room
            </button>
        </div>
    </form>
    
    <div class="mt-4 text-center">
        <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-500">
            Back to Home
        </a>
    </div>
</div>
@endsection