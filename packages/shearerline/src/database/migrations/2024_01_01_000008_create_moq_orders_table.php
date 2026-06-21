<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.moq_orders', 'shearerline_moq_orders'), function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('supplier_id')->nullable()->constrained(config('shearerline.tables.suppliers', 'shearerline_suppliers'))->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->text('customer_remark')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->integer('total_quantity')->default(0);
            $table->integer('shipped_quantity')->default(0);
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->text('refunded_reason')->nullable();
            $table->text('internal_remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('supplier_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.moq_orders', 'shearerline_moq_orders'));
    }
};
