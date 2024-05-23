<?php

namespace App\Http\Requests;

use App\Rules\PhoneNumber;
use Illuminate\Validation\Rule;

class TicketRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', Rule::requiredIf(!$this->wantsJson())],
            'agency_data' => ['nullable', 'array'],
            'agency_data.name' => [
                'string',
                'max:255',
                Rule::requiredIf(!empty($this->get('agency_data')))
            ],
            'agency_data.agents_count' => [
                'integer',
                'digits_between:1,4',
                Rule::requiredIf(!empty($this->get('agency_data')))
            ],
            'agency_data.phone' => [
                'string',
                new PhoneNumber(),
                Rule::requiredIf(!empty($this->get('agency_data')))
            ],
            'agency_data.email' => [
                'email',
                Rule::requiredIf(!empty($this->get('agency_data')))
            ],
            'agency_data.message' => [
                'string',
                'max:500',
                Rule::requiredIf(!empty($this->get('agency_data')))
            ],
        ];
    }
}
