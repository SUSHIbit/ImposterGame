<?php

// app/Http/Requests/JoinRoomRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JoinRoomRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code' => 'required|string|size:5',
        ];
    }
}