<?php

namespace App\Console\Commands;

use App\Models\User\VerificationCode;
use Illuminate\Console\Command;

class ClearExpiredVerificationCodesCommand extends Command
{
    protected $signature = 'clear:expired-verification-codes';

    protected $description = 'Clear expired verification codes';

    public function handle(): void
    {
        VerificationCode::query()
            ->where('expired_at', '<', now()->subMinutes(VerificationCode::VERIFICATION_CODE_TTL))
            ->delete();

        $this->info('Expired verification codes cleared');
    }
}
