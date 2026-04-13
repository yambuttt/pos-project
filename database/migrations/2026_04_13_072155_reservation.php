<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservations', function (Blueprint $table) {
      $table->id();
      $table->string('code', 30)->unique();

      $table->foreignId('reservation_resource_id')->constrained('reservation_resources')->restrictOnDelete();

      $table->string('customer_name', 120);
      $table->string('customer_phone', 30)->nullable();

      $table->dateTime('start_at');
      $table->dateTime('end_at');

      $table->unsignedInteger('pax')->nullable();

      $table->string('menu_type', 20); // REGULAR|BUFFET
      $table->string('status', 30)->default('pending_dp'); // draft|pending_dp|confirmed|checked_in|completed|cancelled|no_show

      // snapshot rule: patokan stok = saat DP paid
      $table->dateTime('stock_snapshot_at')->nullable();

      $table->integer('menu_total')->default(0);
      $table->integer('rental_total')->default(0);
      $table->integer('grand_total')->default(0);

      $table->integer('dp_amount')->default(0);
      $table->integer('paid_amount')->default(0);

      $table->dateTime('dp_paid_at')->nullable();
      $table->dateTime('checked_in_at')->nullable();
      $table->dateTime('checked_out_at')->nullable();

      $table->text('notes')->nullable();

      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

      $table->timestamps();

      $table->index(['status', 'start_at']);
      $table->index(['menu_type', 'status']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservations');
  }
};