<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_shift_overrides', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');

            $table->foreignId('shift_id')->constrained('shifts')->cascadeOnDelete();

            $table->string('status', 20)->default('approved'); // siap kalau nanti mau pending/rejected
            $table->string('reason', 500)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // biar 1 user 1 tanggal cuma 1 override aktif
            $table->unique(['user_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_shift_overrides');
    }
};