<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Imposter Game') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script>
        window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
        
        // Create supabaseClient globally
        window.supabaseClient = {
            createClient: function(supabaseUrl, supabaseKey) {
                return supabase.createClient(supabaseUrl, supabaseKey);
            }
        };
    </script>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen">
    <nav class="bg-indigo-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="text-xl font-bold">Imposter Game</a>
            <div>
                @auth
                    <div class="flex items-center space-x-4">
                        <span>{{ Auth::user()->name }}</span>
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-indigo-200 hover:text-white">Admin</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-indigo-500 hover:bg-indigo-400 px-3 py-1 rounded">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="space-x-2">
                        <a href="{{ route('login') }}" class="text-indigo-200 hover:text-white">Login</a>
                        <a href="{{ route('register') }}" class="bg-indigo-500 hover:bg-indigo-400 px-3 py-1 rounded">Register</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mx-auto px-4 py-6">
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-4 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; 2025 Imposter Game. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>