<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.moq_order_items', 'shearerline_moq_order_items'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('moq_order_id')->constrained(config('shearerline.tables.moq_orders', 'shearerline_moq_orders'))->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained(config('shearerline.tables.products', 'shearerline_products'))->nullOnDelete();
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->integer('shipped_quantity')->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->json('specs')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->index('moq_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.moq_order_items', 'shearerline_moq_order_items'));
    }
};
