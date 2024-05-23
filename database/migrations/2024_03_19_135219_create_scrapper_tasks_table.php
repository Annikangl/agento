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
        Schema::create('scrapper_tasks', function (Blueprint $table) {
            $table->id('task_id');
            $table->unsignedBigInteger('task_start');
            $table->unsignedBigInteger('task_last_update');
            $table->unsignedBigInteger('task_status')->index();
            $table->string('task_type', 128)->index();
            $table->double('task_progress')->nullable();
            $table->string('task_last_msg', 512)->nullable();
            $table->string('task_log_path', 512)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scrapper_tasks');
    }
};
