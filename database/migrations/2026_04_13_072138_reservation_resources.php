<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('reservation_resources', function (Blueprint $table) {
      $table->id();
      $table->string('type', 20); // TABLE|ROOM|HALL
      $table->string('name', 120);
      $table->unsignedInteger('capacity')->default(1);

      $table->integer('hourly_rate')->nullable(); // meja boleh null/0
      $table->integer('flat_rate')->nullable();

      $table->unsignedInteger('min_duration_minutes')->default(60);
      $table->unsignedInteger('buffer_minutes')->default(0);

      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->index(['type', 'is_active']);
    });
  }

  public function down(): void {
    Schema::dropIfExists('reservation_resources');
  }
};