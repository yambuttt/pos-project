<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE inventory_movements
            MODIFY `type` VARCHAR(50) NOT NULL
        ");
    }

    public function down(): void
    {
        // sengaja tidak dikembalikan ke ENUM karena nilai data yang sudah terlanjur
        // masuk (mis. payment_release) bisa membuat rollback gagal / data terpotong.
    }
};