<?php

namespace App\Http\Requests\Customers;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:225',
            'email' => 'nullable|string|email|unique:customers,email',
            'phone' => 'required|numeric|unique:customers,phone',
            'national_code' => 'nullable|numeric|unique:customers,national_code',
            'instagram_id' => 'nullable|string|unique:customers,instagram_id',
            'tel' => 'nullable|numeric',
            'postal_code' => 'nullable|numeric',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
