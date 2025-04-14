<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
        'supabase_id',
    ];

    protected $hidden = [
        'supabase_id',
    ];

    public function hostedRooms()
    {
        return $this->hasMany(Room::class, 'host_user_id');
    }

    public function roomPlayers()
    {
        return $this->hasMany(RoomPlayer::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class, 'voter_id');
    }

    public function imposterRounds()
    {
        return $this->hasMany(Round::class, 'imposter_user_id');
    }

    public function votesReceived()
    {
        return $this->hasMany(Vote::class, 'guessed_user_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
