<?php

use App\Models\Catalogs\Address;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 128)->nullable();
            $table->string('type', 32)->index()->nullable();
            $table->integer('children')->nullable();
            $table->double('lat', 15, 8)->nullable();
            $table->double('lon', 15, 8)->nullable();
            $table->string('path', 256)->index()->nullable();
            $table->integer('count_ads')->index()->nullable();
        });

//        if (Address::count() === 0) {
//            $this->seedTable();
//        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }

    private function seedTable(): void
    {
        echo "\n Seed addresses table\n";

        \Illuminate\Support\Facades\Artisan::call('app:import-addresses');
    }
};
