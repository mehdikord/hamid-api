<?php

namespace App\Http\Requests\Projects\Forms;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectFormsLandingCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'phone'  => 'required|numeric',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
