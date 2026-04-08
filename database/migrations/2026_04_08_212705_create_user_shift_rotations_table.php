<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_shift_rotations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // daily_alternate / weekly_alternate
            $table->string('rotation_type', 30);

            // tanggal mulai pola
            $table->date('start_date');

            // shift pada start_date / start_week
            $table->foreignId('first_shift_id')->constrained('shifts')->cascadeOnDelete();

            // monday/sunday (default monday)
            $table->string('week_starts_on', 10)->default('monday');

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // 1 user maksimal 1 rotation aktif (kita enforce di app, tapi ini bantu)
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_shift_rotations');
    }
};