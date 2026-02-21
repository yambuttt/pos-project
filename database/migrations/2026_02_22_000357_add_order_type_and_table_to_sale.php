<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // dine_in | takeaway
            $table->string('order_type', 20)->default('takeaway')->after('payment_method');

            // nullable karena takeaway tidak perlu meja
            $table->unsignedBigInteger('dining_table_id')->nullable()->after('order_type');

            $table->foreign('dining_table_id')
                ->references('id')
                ->on('dining_tables')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['dining_table_id']);
            $table->dropColumn(['order_type', 'dining_table_id']);
        });
    }
};