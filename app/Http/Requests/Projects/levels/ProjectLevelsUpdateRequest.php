<?php

namespace App\Http\Requests\Projects\levels;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProjectLevelsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $projectId = $this->route('project');
        $levelId = $this->route('level');
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('project_levels', 'name')->where('project_id', $projectId)->ignore($levelId)
            ],
            'description' => 'nullable|string',
            'color' => 'required|string',
            'priority' => 'required|integer|min:1',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
