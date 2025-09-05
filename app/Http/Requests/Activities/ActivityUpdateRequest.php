<?php

namespace App\Http\Requests\Activities;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ActivityUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'admin_id' => 'nullable|exists:admins,id',
            'user_id' => 'nullable|exists:users,id',
            'project_id' => 'nullable|exists:projects,id',
            'customer_id' => 'nullable|exists:customers,id',
            'title' => 'sometimes|required|string|max:255',
            'ip' => 'nullable|ip',
            'device' => 'nullable|string|max:500',
            'activity' => 'sometimes|required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان فعالیت الزامی است.',
            'title.string' => 'عنوان فعالیت باید متن باشد.',
            'title.max' => 'عنوان فعالیت نمی‌تواند بیشتر از 255 کاراکتر باشد.',
            'activity.required' => 'توضیحات فعالیت الزامی است.',
            'activity.string' => 'توضیحات فعالیت باید متن باشد.',
            'admin_id.exists' => 'ادمین انتخاب شده معتبر نیست.',
            'user_id.exists' => 'کاربر انتخاب شده معتبر نیست.',
            'project_id.exists' => 'پروژه انتخاب شده معتبر نیست.',
            'customer_id.exists' => 'مشتری انتخاب شده معتبر نیست.',
            'ip.ip' => 'آدرس IP معتبر نیست.',
            'device.max' => 'اطلاعات دستگاه نمی‌تواند بیشتر از 500 کاراکتر باشد.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
