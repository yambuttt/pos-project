<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservation_material_locks', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
      $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();

      $table->decimal('qty_locked', 14, 3)->default(0);
      $table->decimal('qty_released', 14, 3)->default(0);
      $table->decimal('qty_consumed', 14, 3)->default(0);

      $table->timestamps();

      $table->unique(['reservation_id','raw_material_id']);
      $table->index(['raw_material_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservation_material_locks');
  }
};