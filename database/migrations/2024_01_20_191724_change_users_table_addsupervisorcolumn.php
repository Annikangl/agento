<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use MoonShine\Models\MoonshineUser;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->foreignIdFor(MoonshineUser::class, 'supervisor_id')
                ->after('id')
                ->nullable()
                ->constrained('moonshine_users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeignIdFor(MoonshineUser::class,'supervisor_id');
            $table->dropColumn('supervisor_id');
        });
    }
};
