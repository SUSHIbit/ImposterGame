<!-- resources/views/room/join.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg p-6">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Join a Game Room</h2>
    
    <form action="{{ route('room.join.submit') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label for="code" class="block text-gray-700 font-medium mb-2">Room Code</label>
            <input type="text" id="code" name="code" 
                   class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 uppercase"
                   placeholder="Enter 5-letter code" 
                   maxlength="5" 
                   required
                   value="{{ old('code') }}"
                   pattern="[A-Za-z]{5}"
                   title="5 letters only">
            @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Join Room
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