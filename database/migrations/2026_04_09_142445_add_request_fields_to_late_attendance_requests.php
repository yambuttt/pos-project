<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('late_attendance_requests', function (Blueprint $table) {
            $table->time('requested_until_time')->nullable()->after('date');
            $table->string('evidence_path')->nullable()->after('reason');
        });
    }

    public function down(): void
    {
        Schema::table('late_attendance_requests', function (Blueprint $table) {
            $table->dropColumn(['requested_until_time', 'evidence_path']);
        });
    }
};