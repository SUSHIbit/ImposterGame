<?php

// app/Http/Requests/CreateQuestionSetRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionSetRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->isAdmin();
    }

    public function rules()
    {
        return [
            'normal_question' => 'required|string|max:500',
            'imposter_question' => 'required|string|max:500',
        ];
    }
}