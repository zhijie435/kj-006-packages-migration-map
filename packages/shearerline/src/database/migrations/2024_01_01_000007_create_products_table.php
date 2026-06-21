<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.products', 'shearerline_products'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained(config('shearerline.tables.suppliers', 'shearerline_suppliers'))->nullOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedInteger('moq')->default(1);
            $table->integer('stock')->default(0);
            $table->string('unit')->nullable();
            $table->string('image_url')->nullable();
            $table->json('specs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('supplier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.products', 'shearerline_products'));
    }
};
