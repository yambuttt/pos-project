<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('reservation_id')->nullable()->after('id');
            $table->string('purpose', 20)->default('operational')->after('reservation_id');

            $table->index('reservation_id');
            $table->index('purpose');
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropIndex(['reservation_id']);
            $table->dropIndex(['purpose']);
            $table->dropColumn(['reservation_id', 'purpose']);
        });
    }
};
