<?php

namespace App\Http\Requests\Fields;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class FieldCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $projectId = $this->route('project');
        return [
            'type' => 'required|string',
            'placeholder' => 'nullable|string',
            'default' => 'nullable|string',
            'description' => 'nullable|string',
            'title' => [
                'required',
                'string',
                Rule::unique('fields', 'title')->where('project_id', $projectId)
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
