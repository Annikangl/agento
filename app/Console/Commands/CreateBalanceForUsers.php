<?php

namespace App\Console\Commands;

use App\Exceptions\Api\User\CreateBalanceException;
use App\Models\User\User;
use App\UseCases\Account\BalanceService;
use Illuminate\Console\Command;

class CreateBalanceForUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create empty balance for all users';

    /**
     * Execute the console command.
     * @throws CreateBalanceException
     */
    public function handle(): int
    {
        $service = app(BalanceService::class);
        $users = User::doesntHave('balance')->get();

        $users->each(function (User $user) use ($service) {
            $this->info('Create balance for ' . $user->name);
            $service->createEmptyBalance($user);
        });

        $this->info('Balance created for all users');
        return Command::SUCCESS;
    }
}
