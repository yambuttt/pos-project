<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('waste_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('waste_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();

            $table->decimal('qty', 15, 2);
            $table->decimal('estimated_cost', 15, 2)->default(0); // opsional
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_items');
    }
};
