<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1', 'max:9999999'],
            'delivery_days' => ['required', 'integer', 'min:1', 'max:365'],
            'cover_letter' => ['required', 'string', 'min:20', 'max:5000'],
            'experience_summary' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cover_letter.min' => 'Your proposal should be at least 20 characters so clients understand your approach.',
            'experience_summary.min' => 'Please summarise your relevant experience (at least 10 characters).',
        ];
    }
}
