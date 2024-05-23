<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearPDFsDependencyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear-pdfs-dependency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all other files from pdfs';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $files = Storage::disk('pdfs')->allFiles();

        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if ($extension !== 'pdf') {
                Storage::disk('pdfs')->delete($file);
            }
        }

        return Command::SUCCESS;
    }
}
