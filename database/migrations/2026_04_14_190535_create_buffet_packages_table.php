<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('buffet_packages', function (Blueprint $table) {
      $table->id();
      $table->string('name', 160);
      $table->enum('pricing_type', ['per_pax', 'per_event'])->default('per_pax');
      $table->integer('price')->default(0); // rupiah
      $table->unsignedInteger('min_pax')->nullable();
      $table->boolean('is_active')->default(true);
      $table->text('notes')->nullable();
      $table->timestamps();

      $table->index(['is_active', 'pricing_type']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('buffet_packages');
  }
};