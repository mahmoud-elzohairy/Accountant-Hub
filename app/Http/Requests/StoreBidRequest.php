<?php

namespace App\Http\Requests;

use App\Models\Job;
use Illuminate\Foundation\Http\FormRequest;

class StoreBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $job = $this->route('job');
        $min = $job instanceof Job ? (float) $job->budget_min : 1;
        $max = $job instanceof Job ? (float) $job->budget_max : 9999999;

        return [
            // The proposed price must fall within the client's stated budget range.
            'amount' => ['required', 'numeric', "min:{$min}", "max:{$max}"],
            'delivery_days' => ['required', 'integer', 'min:1', 'max:365'],
            'cover_letter' => ['required', 'string', 'min:20', 'max:5000'],
            'experience_summary' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        $job = $this->route('job');
        $min = $job instanceof Job ? '$'.number_format((float) $job->budget_min) : null;
        $max = $job instanceof Job ? '$'.number_format((float) $job->budget_max) : null;

        return [
            'amount.min' => "Your bid is below the client's budget. It must be at least {$min}.",
            'amount.max' => "Your bid is above the client's budget. It must be at most {$max}.",
            'cover_letter.min' => 'Your proposal should be at least 20 characters so clients understand your approach.',
            'experience_summary.min' => 'Please summarise your relevant experience (at least 10 characters).',
        ];
    }
}
