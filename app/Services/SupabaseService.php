<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SupabaseService
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $headers;

    public function __construct()
    {
        $this->supabaseUrl = Config::get('supabase.url');
        $this->supabaseKey = Config::get('supabase.key');
        $this->headers = [
            'apikey' => $this->supabaseKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function auth()
    {
        return Http::withHeaders($this->headers)->baseUrl("{$this->supabaseUrl}/auth/v1");
    }

    public function db()
    {
        return Http::withHeaders($this->headers)->baseUrl("{$this->supabaseUrl}/rest/v1");
    }

    public function verifyToken($token)
    {
        try {
            $response = $this->auth()
                ->withHeaders(['Authorization' => "Bearer {$token}"])
                ->get('/user');

            if ($response->successful()) {
                Log::info('Supabase authentication successful', ['user' => $response->json()]);
                return $response->json();
            } else {
                Log::warning('Supabase authentication failed', ['status' => $response->status(), 'response' => $response->body()]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Supabase authentication exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $response = $this->db()
                ->withHeaders(['Authorization' => "Bearer {$this->supabaseKey}"])
                ->get('/users', [
                    'email' => 'eq.' . $email,
                    'select' => '*'
                ]);

            return $response->successful() ? $response->json()[0] ?? null : null;
        } catch (\Exception $e) {
            Log::error('Error fetching user by email', ['email' => $email, 'error' => $e->getMessage()]);
            return null;
        }
    }

    public function subscribeToRoom($roomCode, $callback)
    {
        $channel = "room:{$roomCode}";
        return $this->createRealtimeConnection($channel, $callback);
    }

    public function subscribeToRound($roomId, $roundNumber, $callback)
    {
        $channel = "round:{$roomId}:{$roundNumber}";
        return $this->createRealtimeConnection($channel, $callback);
    }

    protected function createRealtimeConnection($channel, $callback)
    {
        // In a real implementation, this would use the Supabase Realtime client
        // For this example, we're just returning the channel name
        return $channel;
    }

    public function broadcastToRoom($roomCode, $event, $payload)
    {
        try {
            // In a real implementation, this would use the Supabase Realtime client
            // For this example, we're just logging the event
            Log::info("Broadcasting to room {$roomCode}: {$event}", $payload);
            return true;
        } catch (\Exception $e) {
            Log::error("Error broadcasting to room {$roomCode}", ['event' => $event, 'error' => $e->getMessage()]);
            return false;
        }
    }
}