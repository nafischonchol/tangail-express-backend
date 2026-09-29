<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            responseError(
                'প্রয়োজনীয় তথ্য সঠিকভাবে পূরণ করুন।',
                422,
                null,
                $validator->errors()
            )
        );
    }
}
