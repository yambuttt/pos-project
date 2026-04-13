<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservation_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();

      $table->string('item_type', 30); // REGULAR_PRODUCT|BUFFET_PACKAGE|BUFFET_ITEM
      $table->unsignedBigInteger('item_id')->nullable(); // product_id / package_id / etc

      $table->string('snapshot_name', 180);
      $table->integer('unit_price')->default(0);
      $table->unsignedInteger('qty')->default(1);
      $table->integer('subtotal')->default(0);

      $table->json('meta')->nullable(); // optional (pax based etc)
      $table->timestamps();

      $table->index(['reservation_id','item_type']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservation_items');
  }
};