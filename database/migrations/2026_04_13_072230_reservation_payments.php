<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservation_payments', function (Blueprint $table) {
      $table->id();
      $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();

      $table->string('type', 20);   // DP|FINAL|REFUND
      $table->integer('amount');
      $table->string('method', 20)->nullable(); // CASH|QRIS|MIDTRANS
      $table->string('status', 20)->default('paid'); // paid|pending|failed
      $table->string('reference', 120)->nullable();
      $table->dateTime('paid_at')->nullable();

      $table->timestamps();

      $table->index(['reservation_id','type']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservation_payments');
  }
};