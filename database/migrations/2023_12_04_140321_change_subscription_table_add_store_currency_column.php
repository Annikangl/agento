<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id')->after('plan_id');
            $table->unsignedBigInteger('original_transaction_id')->after('transaction_id');
            $table->string('event_type')->after('transaction_id');
            $table->string('country_code',10)->nullable()->after('event_type');
            $table->string('currency',10)->nullable()->after('country_code');
            $table->unsignedInteger('price')->after('country_code');
            $table->unsignedInteger('price_in_purchased_currency')->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('transaction_id');
            $table->dropColumn('original_transaction_id');
            $table->dropColumn('event_type');
            $table->dropColumn('country_code');
            $table->dropColumn('currency');
            $table->dropColumn('price');
            $table->dropColumn('price_in_purchased_currency');
        });
    }
};
