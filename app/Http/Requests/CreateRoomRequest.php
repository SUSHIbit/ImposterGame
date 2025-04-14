<?php

// app/Http/Requests/CreateRoomRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoomRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'min_players' => 'sometimes|integer|min:3|max:10',
            'max_players' => 'sometimes|integer|min:3|max:10|gte:min_players',
        ];
    }
}
