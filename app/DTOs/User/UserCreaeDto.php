<?php

namespace App\DTOs\User;

use App\Rules\PhoneNumber;
use Illuminate\Validation\Rules\Password;
use WendellAdriel\ValidatedDTO\ValidatedDTO;

class UserCreaeDto extends ValidatedDTO
{
    public string $name;
    public string $email;
    public string $phone;
    public string $password;
    public string $country;
    public ?string $promocode;
    public ?string $device_name;
    public ?string $fcm_token;


    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'country' => ['required', 'string', 'max:12', 'in:uae,usa'],
            'email' => ['required', 'email', 'unique:users'],
            'phone' => ['nullable', 'unique:users', new PhoneNumber()],
            'password' => ['required', 'confirmed', Password::default()],
            'password_confirmation' => ['required', 'string'],
            'verification_code' => ['required', 'integer', 'digits:4'],
            'promocode' => ['nullable', 'string'],
            'fcm_token' => ['nullable', 'string'],
            'device_name' => ['required', 'string'],
        ];
    }

    protected function defaults(): array
    {
        return [];
    }

    protected function casts(): array
    {
        return [];
    }
}
