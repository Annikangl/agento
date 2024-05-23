<?php

namespace App\Console\Commands;

use App\Models\Catalogs\Address;
use Illuminate\Console\Command;

class ImportAddressesFromCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import address data from CSV file to database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Start import addresses from CSV file');
        Address::truncate();
        $this->info('Truncated addresses table');

        $this->info('Importing addresses from CSV file...');

        $row = 0;
        if (($handle = fopen(storage_path('app/private/property_adr.csv'), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $row++;
                if ($row == 1 || $data == []) continue;

                if ($row % 100 == 0) echo "*";
                Address::query()->create([
                    'name' => trim($data[0]),
                    'type' => trim($data[1]),
                    'children' => trim($data[2]),
                    'lat' => trim($data[3]),
                    'lon' => trim($data[4]),
                    'path' => trim($data[5]),
                    'count_ads' => 0
                ]);
            }
            fclose($handle);
            $this->info("\nImported $row addresses");
        }
    }
}
