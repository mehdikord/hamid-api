<?php

namespace App\Http\Requests\Bot\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SendRequest extends FormRequest
{

    


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|numeric',
            'telegram_id' => 'required|numeric',
        ];
    }
}
