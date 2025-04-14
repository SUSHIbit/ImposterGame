<!-- resources/views/auth/login.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg p-6">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login to Imposter Game</h2>
    
    <div id="auth-content">
        <form id="login-form" class="space-y-4">
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required autocomplete="email">
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Login
                </button>
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500">
                    Don't have an account?
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supabaseUrl = '{{ config('supabase.url') }}';
        const supabaseKey = '{{ config('supabase.key') }}';
        
        // Initialize Supabase client correctly
        const supabase = supabaseClient.createClient(supabaseUrl, supabaseKey);
        
        const loginForm = document.getElementById('login-form');
        const authContent = document.getElementById('auth-content');
        
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            authContent.innerHTML = '<div class="text-center py-6"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><p class="mt-2 text-gray-600">Logging in...</p></div>';
            
            try {
                const { data, error } = await supabase.auth.signInWithPassword({
                    email,
                    password,
                });
                
                if (error) throw error;
                
                if (data && data.session) {
                    const { access_token } = data.session;
                    
                    // Redirect to callback route with token
                    window.location.href = "{{ route('auth.callback') }}?access_token=" + access_token;
                } else {
                    throw new Error('Login failed. No session returned.');
                }
            } catch (error) {
                authContent.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>${error.message || 'An error occurred during login'}</p>
                    </div>
                    ${loginForm.outerHTML}
                `;
                
                // Reattach event listener to the new form
                document.getElementById('login-form').addEventListener('submit', arguments.callee);
            }
        });
    });
</script>
@endpush
@endsection