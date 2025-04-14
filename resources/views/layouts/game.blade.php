<!-- resources/views/layouts/game.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Imposter Game') }} - Game</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
    </script>
    @stack('styles')
</head>
<body class="bg-gray-900 text-white min-h-screen">
    <nav class="bg-gray-800 shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-400">Imposter Game</a>
            <div class="flex items-center space-x-4">
                <span>{{ Auth::user()->name }}</span>
                @yield('game-nav')
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-6">
        @if(session('error'))
            <div class="bg-red-800 border-l-4 border-red-500 text-white p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>