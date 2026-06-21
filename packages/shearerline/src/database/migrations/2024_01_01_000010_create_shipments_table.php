<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.shipments', 'shearerline_shipments'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('moq_order_id')->constrained(config('shearerline.tables.moq_orders', 'shearerline_moq_orders'))->cascadeOnDelete();
            $table->string('shipment_no')->unique();
            $table->string('logistics_company')->nullable();
            $table->string('tracking_no')->nullable();
            $table->json('items')->nullable();
            $table->integer('total_quantity')->default(0);
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('receiver_address')->nullable();
            $table->text('remark')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();

            $table->index('moq_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.shipments', 'shearerline_shipments'));
    }
};
