<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_exception_requests', function (Blueprint $table) {
            $table->id();

            // Pegawai yang mengajukan
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->date('attendance_date');
            $table->string('mode', 10); // in/out

            // device yang dipakai (milik pegawai lain)
            $table->string('device_hash', 64);
            $table->foreignId('device_owner_device_id')->nullable()
                ->constrained('employee_devices')->nullOnDelete();
            $table->foreignId('device_owner_user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('device_name')->nullable();
            $table->string('user_agent', 180)->nullable();

            // lokasi
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);

            // QR yang dipakai (belum di-consume sampai approve)
            $table->foreignId('attendance_qr_token_id')->nullable()
                ->constrained('attendance_qr_tokens')->nullOnDelete();

            // selfie bukti
            $table->string('photo_path')->nullable();

            // alasan darurat
            $table->string('reason', 500)->nullable();

            // status approval admin
            $table->string('status', 20)->default('pending'); // pending/approved/rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('review_note', 500)->nullable();

            $table->timestamps();

            $table->index(['status', 'attendance_date']);
            $table->index(['user_id', 'attendance_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_exception_requests');
    }
};