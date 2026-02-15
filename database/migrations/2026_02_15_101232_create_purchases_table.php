<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();

            $table->enum('source_type', ['supplier', 'external'])->default('external');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source_name')->nullable(); // pasar/random kalau external

            $table->string('invoice_no')->nullable();
            $table->date('purchase_date');
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
