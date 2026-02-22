<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\DiningTable;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dining_tables', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('is_active');
        });

        // backfill untuk data meja yang sudah ada
        DiningTable::whereNull('qr_token')->chunkById(200, function ($rows) {
            foreach ($rows as $t) {
                $t->qr_token = Str::random(32);
                $t->save();
            }
        });

        // setelah terisi, bikin jadi NOT NULL
        Schema::table('dining_tables', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('dining_tables', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};