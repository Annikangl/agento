<?php

namespace App\Http\Requests\Account;

use App\Traits\JsonFailedValidation;
use Illuminate\Foundation\Http\FormRequest;

class CreateWithdrawalRequest extends FormRequest
{
    use JsonFailedValidation;

    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'gt:0'],
        ];
    }


}
