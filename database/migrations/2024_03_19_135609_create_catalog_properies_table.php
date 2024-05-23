<?php

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
        Schema::create('catalog_property', function (Blueprint $table) {
            $table->id();
            $table->boolean('active_flag')->index();
            $table->boolean('update_flag')->index();
            $table->unsignedInteger('data_add')->index();
            $table->unsignedInteger('data_last_update')->index();
            $table->unsignedInteger('miss_try_count');
            $table->string('source', 512);
            $table->integer('listed_date')->nullable();

            $table->string('title')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->string('period', 32)->nullable();

            $table->string('main_photo', 512)->nullable();

            $table->string('property_type', 32);
            $table->string('full_location_path');
            $table->string('property_city', 32);
            $table->string('property_tower', 64);
            $table->string('property_community', 64);
            $table->string('property_subcommunity', 64);

            $table->string('bedrooms', 6)->nullable();
            $table->string('bathrooms', 6)->nullable();

            $table->double('size_sqft')->nullable();
            $table->double('size_m2')->nullable();

            $table->double('geo_lat')->nullable();
            $table->double('geo_lon')->nullable();

            $table->string('reference', 128)->nullable();
            $table->text('description')->nullable();

            $table->string('deal_type', 4);
            $table->text('amenity_names')->nullable();
            $table->string('completion_type', 32)->nullable();
            $table->string('furnished', 9)->nullable();
            $table->unsignedInteger('get_images')->nullable();
            $table->string('rera', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_property');
    }
};
