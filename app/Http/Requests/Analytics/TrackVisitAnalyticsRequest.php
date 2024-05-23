<?php

namespace App\Http\Requests\Analytics;

use App\Traits\JsonFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class TrackVisitAnalyticsRequest extends FormRequest
{
    use JsonFailedValidation;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'device_id' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:255'],
        ];
    }
}
