<?php

use App\Models\User\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('tickets')->whereNull('user_id')->delete();

        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->nullable(false)->change();
        });
    }
};
