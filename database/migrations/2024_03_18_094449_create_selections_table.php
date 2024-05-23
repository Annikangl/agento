<?php

use App\Models\User\User;
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
        Schema::create('selections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('deal_type',5);
            $table->string('property_type', 15);
            $table->string('completion', 10);
            $table->json('beds');
            $table->unsignedInteger('size_from')->nullable();
            $table->unsignedInteger('size_to')->nullable();
            $table->string('location');
            $table->unsignedInteger('budget_from');
            $table->unsignedInteger('budget_to');
            $table->boolean('is_liked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('selections');
    }
};
