<?php

namespace App\Http\Requests\Customer\Order;

use App\Http\Requests\BaseFormRequest;

class StoreOrderRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:1000'],
            'raw_text_list' => ['nullable', 'string', 'max:10000'],
            'image_list' => ['nullable', 'file', 'max:15360'], // 15MB
            'voice_list' => ['nullable', 'file', 'max:30720'], // 30MB
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'কাস্টমারের নাম প্রদান করা আবশ্যক।',
            'phone.required' => 'ফোন নম্বর প্রদান করা আবশ্যক।',
            'address.required' => 'ঠিকানা প্রদান করা আবশ্যক।',
            'image_list.max' => 'ছবির সাইজ সর্বোচ্চ ১৫MB হতে পারবে।',
            'voice_list.max' => 'ভয়েস রেকর্ডিং সর্বোচ্চ ৩০MB হতে পারবে।',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rawText = trim((string) $this->input('raw_text_list'));
            $hasImage = $this->hasFile('image_list');
            $hasVoice = $this->hasFile('voice_list');

            if (empty($rawText) && ! $hasImage && ! $hasVoice) {
                $validator->errors()->add(
                    'list',
                    'দয়া করে বাজারের লিস্ট লিখুন, ছবি দিন অথবা ভয়েস রেকর্ড করুন।'
                );
            }
        });
    }
}
