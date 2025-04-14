<?php

// app/Models/Round.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'round_number',
        'question_set_id',
        'imposter_user_id',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class);
    }

    public function imposter()
    {
        return $this->belongsTo(User::class, 'imposter_user_id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function getVotesForUser($userId)
    {
        return $this->votes()->where('guessed_user_id', $userId)->count();
    }

    public function hasPlayerAnswered($userId)
    {
        return $this->answers()->where('user_id', $userId)->exists();
    }

    public function hasPlayerVoted($userId)
    {
        return $this->votes()->where('voter_id', $userId)->exists();
    }

    public function isImposter($userId)
    {
        return $this->imposter_user_id === $userId;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAnswering()
    {
        return $this->status === 'answering';
    }

    public function isVoting()
    {
        return $this->status === 'voting';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }
}
