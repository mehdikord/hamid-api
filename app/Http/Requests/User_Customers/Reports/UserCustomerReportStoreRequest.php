<?php

namespace App\Http\Requests\User_Customers\Reports;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCustomerReportStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'report' => 'required|string',
            'file' => 'nullable|file|mimes:xlsx,xls,csv,png,jpg,pdf,doc,docx,jpeg,mp4,webm,ogg,mp3,wav,txt',
            'date' => 'required|date',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
