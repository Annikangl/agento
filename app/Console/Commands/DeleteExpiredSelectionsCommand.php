<?php

namespace App\Console\Commands;

use App\Exceptions\Api\Selection\DeleteSelectionException;
use App\Models\Selection\Selection;
use App\UseCases\Selection\SelectionService;
use Illuminate\Console\Command;

class DeleteExpiredSelectionsCommand extends Command
{
    protected $signature = 'expired-selections:delete';

    protected $description = 'Delete expired selections from database.';

    /**
     * @return int
     * @throws DeleteSelectionException
     */
    public function handle(): int
    {
        $selectionService = app(SelectionService::class);

        $expiredSelections = Selection::query()
            ->where('expired_at', '<', now())
            ->get();

        if ($expiredSelections->isEmpty()) {
            $this->info('No expired selections found');
            return Command::SUCCESS;
        }

        $this->info('Expired selections found: ' . $expiredSelections->count());
        $count = 0;

        $expiredSelections->each(function (Selection $selection) use ($selectionService, &$count) {
            $selectionService->delete($selection);
            $count++;
        });

        $this->info('Total selections deleted: ' . $count);
        return Command::SUCCESS;
    }
}
