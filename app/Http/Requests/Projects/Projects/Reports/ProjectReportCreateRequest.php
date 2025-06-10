<?php

namespace App\Http\Requests\Projects\Projects\Reports;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectReportCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'project_customer_id' => 'required|exists:project_customers,id',
            'user_id' => 'nullable|exists:users,id',
            'report' => 'required',
            'file' => 'nullable|file|mimes:xlsx,xls,csv,png,jpg,pdf,doc,docx,jpeg,mp4,webm,ogg,mp3,wav,txt',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
