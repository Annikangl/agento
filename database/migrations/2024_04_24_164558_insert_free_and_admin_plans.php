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
        \App\Models\User\Plan::query()->create([
            'name' => \App\Models\User\Plan::FREE_PLAN,
            'price' => 0,
            'duration' => 7,
            'description' => 'Free plan for new users',
        ]);

        \App\Models\User\Plan::query()->create([
            'name' => \App\Models\User\Plan::FROM_ADMIN,
            'price' => 0,
            'duration' => 1,
            'description' => 'Admin plan',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User\Plan::query()->whereIn('name', [
            \App\Models\User\Plan::FREE_PLAN,
            \App\Models\User\Plan::FROM_ADMIN,
        ])->delete();
    }
};
