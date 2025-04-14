<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('admin-title', 'Admin Dashboard')

@section('admin-content')
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Game Statistics</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Total Users:</span>
                <span class="font-semibold">{{ \App\Models\User::count() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Total Rooms:</span>
                <span class="font-semibold">{{ \App\Models\Room::count() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Active Games:</span>
                <span class="font-semibold">{{ \App\Models\Room::where('status', 'in_progress')->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Question Sets:</span>
                <span class="font-semibold">{{ \App\Models\QuestionSet::count() }}</span>
            </div>
        </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.question-sets') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-2 px-4 rounded">
                Manage Question Sets
            </a>
            <a href="{{ route('home') }}" class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded">
                Return to Game
            </a>
        </div>
    </div>
</div>

<div class="mt-8 bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b text-left">Room</th>
                    <th class="py-2 px-4 border-b text-left">Host</th>
                    <th class="py-2 px-4 border-b text-left">Status</th>
                    <th class="py-2 px-4 border-b text-left">Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Room::with('host')->latest()->take(10)->get() as $room)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $room->code }}</td>
                    <td class="py-2 px-4 border-b">{{ $room->host->name }}</td>
                    <td class="py-2 px-4 border-b">
                        @if($room->status == 'waiting')
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Waiting</span>
                        @elseif($room->status == 'in_progress')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">In Progress</span>
                        @else
                            <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Completed</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 border-b">{{ $room->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection