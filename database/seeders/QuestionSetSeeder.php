<?php

// database/seeders/QuestionSetSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionSet;
use App\Models\Question;

class QuestionSetSeeder extends Seeder
{
    public function run()
    {
        $questionSets = [
            [
                'normal' => 'Name your favorite movie and explain why you like it.',
                'imposter' => 'Name your favorite book and explain why you like it.'
            ],
            [
                'normal' => 'If you could have any superpower, what would it be and why?',
                'imposter' => 'If you could be any animal, what would you be and why?'
            ],
            [
                'normal' => 'Describe your ideal vacation destination.',
                'imposter' => 'Describe your ideal place to live.'
            ],
            [
                'normal' => "What's your favorite food and how would you describe it to someone who's never tried it?",
                'imposter' => "What's your favorite restaurant and what do you usually order there?"
            ],
            [
                'normal' => 'If you could meet any historical figure, who would it be and what would you ask them?',
                'imposter' => 'If you could meet any fictional character, who would it be and what would you ask them?'
            ],
            [
                'normal' => "What's the best advice you've ever received?",
                'imposter' => "What's a life lesson you had to learn the hard way?"           ],
            [
                'normal' => 'If you could time travel, which era would you visit and why?',
                'imposter' => 'If you could live in any country, which would you choose and why?'
            ],
            [
                'normal' => 'What hobby or activity do you enjoy that might surprise people?',
                'imposter' => "What's a skill you'd like to learn or improve?"
            ],
            [
                'normal' => 'What was your dream job as a child?',
                'imposter' => "What's your current dream job?"
            ],
            [
                'normal' => "What's the most beautiful place you've ever visited?",
                'imposter' => "What's a place you've always wanted to visit but haven't yet?"
            ]
        ];

        foreach ($questionSets as $index => $set) {
            $questionSet = QuestionSet::create([
                'set_number' => $index + 1,
            ]);

            // Create normal question
            Question::create([
                'question_set_id' => $questionSet->id,
                'content' => $set['normal'],
                'is_imposter_question' => false,
            ]);

            // Create imposter question
            Question::create([
                'question_set_id' => $questionSet->id,
                'content' => $set['imposter'],
                'is_imposter_question' => true,
            ]);
        }

        $this->command->info('Question sets created successfully.');
    }
}