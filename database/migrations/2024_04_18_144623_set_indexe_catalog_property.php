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
        Schema::table('catalog_property', function (Blueprint $table) {
            $table->index(
                [
                    'active_flag',
                    'deal_type',
                    'property_type',
                    'completion_type',
                    'bedrooms',
                    'property_city',
                    'property_community',
                    'property_subcommunity',
                    'property_tower',
                    'price',
                    'size_sqft',
                    'size_m2'
                ],
                'catalog_property_mulptiple_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_property', function (Blueprint $table) {
            $table->dropIndex('catalog_property_mulptiple_index');
        });
    }
};
