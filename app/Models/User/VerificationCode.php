<?php

namespace App\Models\User;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{
    use HasFactory;

    const VERIFICATION_CODE_TTL = 10;

    protected $fillable = [
        'email',
        'code',
        'expired_at'
    ];

    /**
     * Check exists verification code by email
     * @param string $email
     * @param string $code
     * @return bool
     */
    public static function checkVerificationCode(string $email, string $code): bool
    {
        return VerificationCode::query()->where('email', $email)
            ->where('code', $code)
            ->exists();
    }

    /**
     * Create a verification code
     * @param string $email
     * @return VerificationCode
     * @throws Exception
     */
    public static function createVerificationCode(string $email): VerificationCode
    {
        return self::create([
            'email' => $email,
            'code' => random_int(1000,9999),
            'expired_at' => now()->addMinutes(self::VERIFICATION_CODE_TTL)
        ]);
    }

    /**
     * Remove verification code by email
     * @param string $email
     * @return void
     */
    public static function clearVerificationCode(string $email): void
    {
         self::query()->where('email', $email)->delete();
    }
}
