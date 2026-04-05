<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('device_hash', 64)->nullable()->after('device');

            $table->decimal('check_in_lat', 10, 7)->nullable()->after('check_in_at');
            $table->decimal('check_in_lng', 10, 7)->nullable()->after('check_in_lat');
            $table->decimal('check_out_lat', 10, 7)->nullable()->after('check_out_at');
            $table->decimal('check_out_lng', 10, 7)->nullable()->after('check_out_lat');

            $table->string('check_in_photo_path')->nullable()->after('check_in_lng');
            $table->string('check_out_photo_path')->nullable()->after('check_out_lng');

            $table->foreignId('check_in_qr_id')->nullable()->after('device_hash')
                ->constrained('attendance_qr_tokens')->nullOnDelete();
            $table->foreignId('check_out_qr_id')->nullable()->after('check_in_qr_id')
                ->constrained('attendance_qr_tokens')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('check_in_qr_id');
            $table->dropConstrainedForeignId('check_out_qr_id');
            $table->dropColumn([
                'device_hash',
                'check_in_lat','check_in_lng','check_out_lat','check_out_lng',
                'check_in_photo_path','check_out_photo_path'
            ]);
        });
    }
};