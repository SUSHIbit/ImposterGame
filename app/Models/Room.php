<?php

// app/Models/Room.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'host_user_id',
        'status',
        'min_players',
        'max_players',
        'current_round',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function players()
    {
        return $this->hasMany(RoomPlayer::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function currentRound()
    {
        return $this->rounds()->where('round_number', $this->current_round)->first();
    }

    public function isWaiting()
    {
        return $this->status === 'waiting';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function canStart()
    {
        return $this->isWaiting() && $this->players->count() >= $this->min_players;
    }

    public function canJoin()
    {
        return $this->isWaiting() && $this->players->count() < $this->max_players;
    }
}
