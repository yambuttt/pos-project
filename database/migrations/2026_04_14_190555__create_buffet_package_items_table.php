<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('buffet_package_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('buffet_package_id')->constrained('buffet_packages')->cascadeOnDelete();
      $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
      $table->unsignedInteger('qty')->default(1);
      $table->string('note', 180)->nullable();
      $table->timestamps();

      $table->unique(['buffet_package_id', 'product_id']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('buffet_package_items');
  }
};