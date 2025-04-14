<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SupabaseService;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function handleCallback(Request $request)
    {
        $token = $request->input('access_token');
        
        if (!$token) {
            return redirect()->route('login')->with('error', 'Authentication failed. No token provided.');
        }
        
        try {
            $supabaseUser = $this->supabaseService->verifyToken($token);
            
            if (!$supabaseUser) {
                return redirect()->route('login')->with('error', 'Invalid token or authentication failed.');
            }
            
            // Find or create user
            $user = User::firstOrCreate(
                ['supabase_id' => $supabaseUser['id']],
                [
                    'email' => $supabaseUser['email'],
                    'name' => $supabaseUser['user_metadata']['name'] ?? explode('@', $supabaseUser['email'])[0],
                    'role' => 'user',
                ]
            );
            
            Auth::login($user);
            
            // Store token in cookie - secure and HTTP only
            $cookie = Cookie::make('supabase_token', $token, 60 * 24, null, null, true, true);
            
            return redirect()->route('home')->withCookie($cookie);
            
        } catch (\Exception $e) {
            \Log::error('Authentication error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Authentication error: ' . $e->getMessage());
        }
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        $cookie = Cookie::forget('supabase_token');
        
        return redirect()->route('login')->withCookie($cookie);
    }
}