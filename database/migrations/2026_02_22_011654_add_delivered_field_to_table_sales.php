<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('kitchen_done_at');
            $table->unsignedBigInteger('delivered_user_id')->nullable()->after('delivered_at');

            $table->foreign('delivered_user_id')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['delivered_user_id']);
            $table->dropColumn(['delivered_at', 'delivered_user_id']);
        });
    }
};