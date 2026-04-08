<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // cuti / sakit
            $table->string('type', 20);

            // range tanggal (untuk cuti bisa beberapa hari, sakit bisa 1 hari)
            $table->date('start_date');
            $table->date('end_date');

            $table->string('reason', 500)->nullable();

            // wajib untuk sakit
            $table->string('doctor_note_path')->nullable();

            // status approval
            $table->string('status', 20)->default('pending'); // pending/approved/rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note', 500)->nullable();

            $table->timestamps();

            $table->index(['status', 'type']);
            $table->index(['user_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};