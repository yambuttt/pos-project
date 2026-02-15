<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();

            $table->decimal('system_qty', 15, 2)->default(0);
            $table->decimal('physical_qty', 15, 2)->default(0);
            $table->decimal('difference', 15, 2)->default(0); // physical - system
            $table->text('note')->nullable();

            $table->timestamps();

            $table->unique(['stock_opname_id','raw_material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
