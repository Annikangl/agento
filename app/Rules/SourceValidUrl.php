<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class SourceValidUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = str_replace('https://m.', 'https://', $value);

        if (!preg_match('/^https:\/\/(www\.bayut\.com|www\.propertyfinder\.ae|(?:abudhabi|dubai|sharjah|ajman|alain|rak|fujairah|uaq)\.dubizzle\.com|dubizzle\.com)\/\S*$/i', $value)) {
            $fail('Invalid URL format');
        }
    }
}
