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
        Schema::create('catalog_property_img', function (Blueprint $table) {
            $table->id('img_id');
            $table->unsignedBigInteger('id')->index();
            $table->string('path', 512);
            $table->string('type', 128);
            $table->foreign('id')
                ->references('id')
                ->on('catalog_property')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_property_img');
    }
};
