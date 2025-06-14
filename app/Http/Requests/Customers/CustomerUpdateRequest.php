<?php

namespace App\Http\Requests\Customers;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CustomerUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:225',
            'email' => "nullable|string|email|unique:customers,email,".$this->customer->id,
            'phone' => "required|numeric|unique:customers,phone,".$this->customer->id,
            'national_code' => "nullable|numeric|unique:customers,national_code,".$this->customer->id,
            'instagram_id' => "nullable|string|unique:customers,instagram_id,".$this->customer->id,
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
