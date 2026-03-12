<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('payment_status')->default('paid')->after('payment_method');
            $table->string('midtrans_order_id')->nullable()->after('payment_status');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id');
            $table->string('midtrans_transaction_status')->nullable()->after('midtrans_transaction_id');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_status');
            $table->json('midtrans_response')->nullable()->after('midtrans_payment_type');
            $table->timestamp('payment_expires_at')->nullable()->after('midtrans_response');
            $table->timestamp('paid_at')->nullable()->after('payment_expires_at');
            $table->timestamp('stock_released_at')->nullable()->after('paid_at');

            $table->index('payment_status');
            $table->index('midtrans_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['midtrans_order_id']);

            $table->dropColumn([
                'payment_status',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_transaction_status',
                'midtrans_payment_type',
                'midtrans_response',
                'payment_expires_at',
                'paid_at',
                'stock_released_at',
            ]);
        });
    }
};