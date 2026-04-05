<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_code', 40)->unique();

            // reservable: table/room
            $table->string('reservable_type', 10); // table | room
            $table->unsignedBigInteger('reservable_id');

            $table->string('customer_name');
            $table->string('customer_phone', 40);
            $table->unsignedInteger('party_size');

            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('duration_minutes');

            $table->string('status', 20)->default('pending');
            $table->string('source', 20)->default('online'); // online/admin/walkin

            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['reservable_type', 'reservable_id', 'reservation_date']);
            $table->index(['reservation_date', 'start_time']);
            $table->index('status');
        });

        Schema::create('reservation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('qty');
            $table->decimal('price_snapshot', 14, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();

            $table->index('reservation_id');
            $table->index('product_id');

            $table->foreign('reservation_id')->references('id')->on('reservations')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::create('reservation_material_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('raw_material_id');
            $table->decimal('required_qty', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(['reservation_id', 'raw_material_id'], 'res_req_unique');

            $table->foreign('reservation_id')->references('id')->on('reservations')->cascadeOnDelete();
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->cascadeOnDelete();
        });

        Schema::create('reservation_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->unsignedBigInteger('raw_material_id');
            $table->string('type', 30); // purchase_in, allocate_from_main, consume, return_to_main, waste
            $table->decimal('qty', 14, 4);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['reservation_id', 'raw_material_id']);
            $table->index('type');

            $table->foreign('reservation_id')->references('id')->on('reservations')->cascadeOnDelete();
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_inventory_movements');
        Schema::dropIfExists('reservation_material_requirements');
        Schema::dropIfExists('reservation_items');
        Schema::dropIfExists('reservations');
    }
};
