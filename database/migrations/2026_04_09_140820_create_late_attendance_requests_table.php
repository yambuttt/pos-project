<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('late_attendance_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date'); // tanggal telat

            // batas maksimal check-in yg diset admin (misal 12:00)
            $table->time('allowed_until_time')->nullable();

            $table->string('reason', 500)->nullable();

            $table->string('status', 20)->default('pending'); // pending/approved/rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note', 500)->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['status', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('late_attendance_requests');
    }
};