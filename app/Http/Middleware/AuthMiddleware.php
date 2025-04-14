<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SupabaseService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthMiddleware
{
    protected $supabaseService;

    public function __construct(SupabaseService $supabaseService)
    {
        $this->supabaseService = $supabaseService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->cookie('supabase_token');

        if (!$token) {
            return redirect()->route('login');
        }

        $supabaseUser = $this->supabaseService->verifyToken($token);

        if (!$supabaseUser) {
            return redirect()->route('login');
        }

        // Find or create the user in our database
        $user = User::firstOrCreate(
            ['supabase_id' => $supabaseUser['id']],
            [
                'email' => $supabaseUser['email'],
                'name' => $supabaseUser['user_metadata']['name'] ?? explode('@', $supabaseUser['email'])[0],
                'role' => 'user',
            ]
        );

        Auth::login($user);

        return $next($request);
    }
}