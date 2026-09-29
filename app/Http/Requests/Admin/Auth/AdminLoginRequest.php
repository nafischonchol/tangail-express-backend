<?php

namespace App\Http\Requests\Admin\Auth;

use App\Http\Requests\BaseFormRequest;

class AdminLoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required_without_all:phone,login', 'nullable', 'string'],
            'phone' => ['required_without_all:email,login', 'nullable', 'string'],
            'login' => ['required_without_all:email,phone', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required_without_all' => 'ইমেইল অথবা ফোন নম্বর প্রদান করুন।',
            'phone.required_without_all' => 'ইমেইল অথবা ফোন নম্বর প্রদান করুন।',
            'login.required_without_all' => 'ইমেইল অথবা ফোন নম্বর প্রদান করুন।',
            'password.required' => 'পাসওয়ার্ড প্রদান করুন।',
        ];
    }
}
