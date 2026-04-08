<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // fixed / rotation
            $table->string('shift_scheme', 20)->default('fixed')->after('role');

            // shift default untuk user (dipakai jika fixed, atau fallback)
            $table->foreignId('default_shift_id')
                ->nullable()
                ->after('shift_scheme')
                ->constrained('shifts')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_shift_id');
            $table->dropColumn('shift_scheme');
        });
    }
};