<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservation_buffet_movements', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
      $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();

      // purchase | transfer_in | return_to_main | consume | waste | adjustment
      $table->string('type', 40);

      $table->decimal('qty_in', 15, 2)->default(0);
      $table->decimal('qty_out', 15, 2)->default(0);

      $table->decimal('unit_cost', 15, 2)->nullable(); // opsional untuk purchase
      $table->text('note')->nullable();

      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();

      $table->index(['reservation_id', 'type']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservation_buffet_movements');
  }
};