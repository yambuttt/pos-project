<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // status kitchen: new | processing | done
            $table->string('kitchen_status')->default('new')->after('status');
            $table->timestamp('kitchen_started_at')->nullable()->after('kitchen_status');
            $table->timestamp('kitchen_done_at')->nullable()->after('kitchen_started_at');

            // opsional kalau mau tau siapa yang handle di kitchen
            $table->unsignedBigInteger('kitchen_user_id')->nullable()->after('kitchen_done_at');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['kitchen_status', 'kitchen_started_at', 'kitchen_done_at', 'kitchen_user_id']);
        });
    }
};