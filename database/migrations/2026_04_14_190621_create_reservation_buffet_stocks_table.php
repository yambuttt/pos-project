<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservation_buffet_stocks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
      $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();

      $table->decimal('qty_on_hand', 15, 2)->default(0);
      $table->decimal('avg_cost', 15, 2)->nullable(); // opsional

      $table->timestamps();
      $table->unique(['reservation_id', 'raw_material_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservation_buffet_stocks');
  }
};