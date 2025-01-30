<?php

namespace App\Http\Requests\Projects\Projects;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'project_category_id' => 'nullable|integer|exists:project_categories,id',
            'project_status_id' => 'nullable|integer|exists:project_statuses,id',
            'name' => 'required|string|unique:projects',
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'manager_name' => 'nullable|string',
            'manager_phone' => 'nullable|string',

        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
