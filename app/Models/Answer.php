<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'user_id',
        'answer_text',
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}