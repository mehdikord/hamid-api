<?php

namespace App\Http\Requests\Projects\Projects;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProjectUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:project_categories,name,'.$this->project->id,
            'project_category_id' => 'nullable|integer|exists:project_categories,id',
            'project_status_id' => 'nullable|integer|exists:project_statuses,id',
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
