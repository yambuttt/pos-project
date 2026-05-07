<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categories
        Schema::create('toko_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Products
        Schema::create('toko_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_category_id')->nullable()->constrained('toko_categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->integer('stock')->default(0);
            $table->boolean('has_variants')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Product Variants
        Schema::create('toko_product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_product_id')->constrained('toko_products')->cascadeOnDelete();
            $table->string('name'); // e.g., "Red - L", "1 Kg", etc.
            $table->string('sku')->unique()->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // 4. Purchases (Barang Masuk)
        Schema::create('toko_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique(); // e.g., PO-TOKO-001
            $table->date('date');
            $table->string('supplier')->nullable();
            $table->unsignedBigInteger('total_cost')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('toko_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_purchase_id')->constrained('toko_purchases')->cascadeOnDelete();
            $table->morphs('purchasable'); // can be toko_product or toko_product_variant
            $table->integer('qty');
            $table->unsignedBigInteger('unit_cost')->default(0);
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });

        // 5. Wastes (Barang Waste)
        Schema::create('toko_wastes', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('toko_waste_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_waste_id')->constrained('toko_wastes')->cascadeOnDelete();
            $table->morphs('wasteable');
            $table->integer('qty');
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        // 6. Outbounds (Barang Keluar Manual, e.g., for store display, damaged, return to supplier)
        Schema::create('toko_outbounds', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->date('date');
            $table->string('destination')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('toko_outbound_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_outbound_id')->constrained('toko_outbounds')->cascadeOnDelete();
            $table->morphs('outboundable');
            $table->integer('qty');
            $table->timestamps();
        });

        // 7. Stock Opnames
        Schema::create('toko_stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('toko_stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('toko_stock_opname_id')->constrained('toko_stock_opnames')->cascadeOnDelete();
            $table->morphs('opnameable');
            $table->integer('system_stock');
            $table->integer('actual_stock');
            $table->integer('difference');
            $table->timestamps();
        });

        // 8. Inventory Movements (Ledger)
        Schema::create('toko_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->morphs('item'); // toko_product or toko_product_variant
            $table->string('type'); // in, out, waste, adjustment, sale
            $table->integer('qty');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->nullableMorphs('reference'); // e.g. toko_purchase, toko_waste
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toko_inventory_movements');
        Schema::dropIfExists('toko_stock_opname_items');
        Schema::dropIfExists('toko_stock_opnames');
        Schema::dropIfExists('toko_outbound_items');
        Schema::dropIfExists('toko_outbounds');
        Schema::dropIfExists('toko_waste_items');
        Schema::dropIfExists('toko_wastes');
        Schema::dropIfExists('toko_purchase_items');
        Schema::dropIfExists('toko_purchases');
        Schema::dropIfExists('toko_product_variants');
        Schema::dropIfExists('toko_products');
        Schema::dropIfExists('toko_categories');
    }
};
