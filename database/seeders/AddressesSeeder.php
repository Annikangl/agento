<?php

namespace Database\Seeders;

use App\Models\Catalogs\Address;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Import data from csv file
     */
    public function run(): void
    {
//        $row = 0;
//        if (($handle = fopen(storage_path('app/private/property_adr.csv'), "r")) !== FALSE) {
//            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
//                $row++;
//                if ($row == 1 || $data == []) continue;
//
//                if ($row % 100 == 0) echo "*";
//                Address::query()->create([
//                    'name' => trim($data[0]),
//                    'type' => trim($data[1]),
//                    'children' => trim($data[2]),
//                    'lat' => trim($data[3]),
//                    'lon' => trim($data[4]),
//                    'path' => trim($data[5]),
//                    'count_ads' => 0
//                ]);
//            }
//            fclose($handle);
//            echo "\nImport addresses completed\n";
//        }
    }
}
