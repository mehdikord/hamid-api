<?php

namespace App\Http\Requests\ImportMethods;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ImportMethodUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        $projectId = $this->route('project');
        $importMethodId = $this->route('import_method');

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('import_methods', 'name')
                    ->where('project_id', $projectId)
                    ->ignore($importMethodId)
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(),422));
    }
}


?>
