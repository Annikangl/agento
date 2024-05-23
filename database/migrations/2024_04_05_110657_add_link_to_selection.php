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
        Schema::table('selections', function (Blueprint $table) {
            $table->string('uniqueid')->after('id')->nullable();
            $table->string('web_link')->after('is_liked')->nullable();
        });

        DB::table('selections')->whereNull('uniqueid')->get()->each(function ($selection) {
            DB::table('selections')->where('id', $selection->id)->update([
                'uniqueid' => uniqid()
            ]);
        });

        Schema::table('selections', function (Blueprint $table) {
            $table->unique('uniqueid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('selections', function (Blueprint $table) {
            $table->dropColumn('web_link');
            $table->dropColumn('uniqueid');
        });
    }
};
