<?php

use App\Models\User\MoonshineUser;
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
        Schema::table('promocodes', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->after('id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(MoonshineUser::class, 'supervisor_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promocodes', function (Blueprint $table) {
            $table->dropForeignIdFor(User::class);
            $table->dropColumn('user_id');

            $table->foreignIdFor(MoonshineUser::class, 'supervisor_id')
                ->nullable(false)
                ->change();
        });
    }
};
