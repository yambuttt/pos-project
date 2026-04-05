<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('face_profiles');
    }

    public function down(): void
    {
        // optional: kalau mau rollback, bisa recreate lagi, tapi karena kita benar-benar hapus fitur ini,
        // biasanya down() dibiarkan kosong atau recreate sesuai kebutuhan.
    }
};