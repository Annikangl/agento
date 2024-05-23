<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('moonshine_users', function (Blueprint $table) {
            $table->string('phone',20)->nullable()->after('email');
            $table->integer('balance')->default(0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('moonshine_users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('balance');
        });
    }
};
