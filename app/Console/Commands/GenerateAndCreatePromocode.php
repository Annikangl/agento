<?php

namespace App\Console\Commands;

use App\Exceptions\Api\Promocode\PromocodeException;
use App\Models\User\User;
use App\UseCases\PromoCodeService;
use Illuminate\Console\Command;

class GenerateAndCreatePromocode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate-promocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $service = app(PromoCodeService::class);

        $users = User::query()->doesntHave('promocode')->get();

        $users->each(function (User $user) use ($service) {
            $this->info('Generate promocode for user ' . $user->id);
            try {
                $service->create($user);
            } catch (PromocodeException $exception) {
                $this->info($exception->getMessage());

            }
            return Command::FAILURE;
        });

        return Command::SUCCESS;
    }
}
