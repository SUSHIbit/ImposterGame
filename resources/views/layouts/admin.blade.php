<!-- resources/views/layouts/admin.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="flex">
        <div class="w-64 bg-gray-800 text-white p-4 min-h-screen">
            <h2 class="text-xl font-bold mb-4">Admin Panel</h2>
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="block py-2 px-4 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.question-sets') }}" class="block py-2 px-4 rounded {{ request()->routeIs('admin.question-sets') ? 'bg-gray-700' : 'hover:bg-gray-700' }}">
                        Question Sets
                    </a>
                </li>
            </ul>
        </div>
        <div class="flex-1 p-6">
            <h1 class="text-2xl font-bold mb-6">@yield('admin-title')</h1>
            @yield('admin-content')
        </div>
    </div>
@endsection