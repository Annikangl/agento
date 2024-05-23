<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::table('catalog_property')
            ->leftJoin('catalog_property_img', 'catalog_property_img.id', '=', 'catalog_property.id')
            ->whereNull('catalog_property_img.id')
            ->where('catalog_property.active_flag', 1)
            ->update([
                'get_images' => 1
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('catalog_property')
            ->leftJoin('catalog_property_img', 'catalog_property_img.id', '=', 'catalog_property.id')
            ->whereNull('catalog_property_img.id')
            ->where('catalog_property.active_flag', 1)
            ->update([
                'get_images' => 0
            ]);
    }
};
