<?php

namespace App\Http\Requests\Projects\Statuses;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProjectStatusUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $projectId = $this->route('project');
        $statusId = $this->route('status');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('project_customer_statuses', 'name')->where('project_id', $projectId)->ignore($statusId)
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
