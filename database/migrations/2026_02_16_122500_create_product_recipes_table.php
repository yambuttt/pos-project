<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();

            // qty bahan per 1 porsi produk (ex: 18 gram coffee)
            $table->decimal('qty', 14, 3);

            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
    }
};
