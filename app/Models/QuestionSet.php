<?php

// app/Models/QuestionSet.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
    use HasFactory;

    protected $fillable = [
        'set_number',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function getNormalQuestion()
    {
        return $this->questions()->where('is_imposter_question', false)->first();
    }

    public function getImposterQuestion()
    {
        return $this->questions()->where('is_imposter_question', true)->first();
    }
}