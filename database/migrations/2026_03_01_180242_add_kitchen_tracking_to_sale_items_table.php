<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->unsignedInteger('kitchen_cooked_qty')->default(0)->after('qty');
            $table->timestamp('kitchen_started_at')->nullable()->after('kitchen_cooked_qty');
            $table->timestamp('kitchen_done_at')->nullable()->after('kitchen_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn(['kitchen_cooked_qty', 'kitchen_started_at', 'kitchen_done_at']);
        });
    }
};