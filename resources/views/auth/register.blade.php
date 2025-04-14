<!-- resources/views/auth/register.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg overflow-hidden shadow-lg p-6">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Register for Imposter Game</h2>
    
    <div id="auth-content">
        <form id="register-form" class="space-y-4">
            <div>
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            
            <div>
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
            </div>
            
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Register
                </button>
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">
                    Already have an account?
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supabaseUrl = '{{ config('supabase.url') }}';
        const supabaseKey = '{{ config('supabase.key') }}';
        
        // Create Supabase client properly
        const supabase = supabaseClient.createClient(supabaseUrl, supabaseKey);
        
        const registerForm = document.getElementById('register-form');
        const authContent = document.getElementById('auth-content');
        
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            if (password !== passwordConfirmation) {
                alert('Passwords do not match');
                return;
            }
            
            authContent.innerHTML = '<div class="text-center py-6"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div><p class="mt-2 text-gray-600">Creating your account...</p></div>';
            
            try {
                const { data, error } = await supabase.auth.signUp({
                    email,
                    password,
                    options: {
                        data: {
                            name,
                        }
                    }
                });
                
                if (error) throw error;
                
                if (data && data.session) {
                    const { access_token } = data.session;
                    
                    // Redirect to callback route with token
                    window.location.href = "{{ route('auth.callback') }}?access_token=" + access_token;
                } else {
                    // Show confirmation message if email confirmation is required
                    authContent.innerHTML = `
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>Registration successful! Please check your email to confirm your account.</p>
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('login') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500">Go to Login</a>
                        </div>
                    `;
                }
            } catch (error) {
                authContent.innerHTML = `
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>${error.message || 'An error occurred during registration'}</p>
                    </div>
                    ${registerForm.outerHTML}
                `;
                
                // Reattach event listener to the new form
                document.getElementById('register-form').addEventListener('submit', arguments.callee);
            }
        });
    });
</script>
@endpush
@endsection