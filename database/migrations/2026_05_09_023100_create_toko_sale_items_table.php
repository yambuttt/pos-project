<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toko_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('toko_product_id')->constrained()->onDelete('restrict');
            $table->foreignId('toko_product_variant_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->integer('qty');
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toko_sale_items');
    }
};
