<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

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

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
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
        // In a real implementation, this would use the Supabase Realtime client
        // For this example, we're just logging the event
        \Log::info("Broadcasting to room {$roomCode}: {$event}", $payload);
        return true;
    }
}