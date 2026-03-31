<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('raw_materials', 'reserved_stock')) {
                $table->decimal('reserved_stock', 18, 3)->default(0)->after('stock_on_hand');
            }
        });
    }

    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            if (Schema::hasColumn('raw_materials', 'reserved_stock')) {
                $table->dropColumn('reserved_stock');
            }
        });
    }
};