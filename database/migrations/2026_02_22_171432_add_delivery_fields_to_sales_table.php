

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {

            // Taruh setelah dining_table_id (kolom ini sudah ada di project kamu)
            $table->string('delivery_phone', 30)->nullable()->after('dining_table_id');
            $table->text('delivery_address')->nullable()->after('delivery_phone');

            $table->decimal('delivery_lat', 10, 7)->nullable()->after('delivery_address');
            $table->decimal('delivery_lng', 10, 7)->nullable()->after('delivery_lat');

            $table->decimal('delivery_distance_km', 8, 2)->nullable()->after('delivery_lng');

            // default 0 biar aman untuk non-delivery
            $table->decimal('delivery_fee', 12, 2)->default(0)->after('delivery_distance_km');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_phone',
                'delivery_address',
                'delivery_lat',
                'delivery_lng',
                'delivery_distance_km',
                'delivery_fee',
            ]);
        });
    }
};