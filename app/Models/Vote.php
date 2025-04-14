<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'voter_id',
        'guessed_user_id',
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function voter()
    {
        return $this->belongsTo(User::class, 'voter_id');
    }

    public function guessedUser()
    {
        return $this->belongsTo(User::class, 'guessed_user_id');
    }
}