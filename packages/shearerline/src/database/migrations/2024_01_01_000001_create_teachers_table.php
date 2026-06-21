<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.teachers', 'shearerline_teachers'), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('title')->nullable();
            $table->text('bio')->nullable();
            $table->json('specialties')->nullable();
            $table->unsignedInteger('experience_years')->default(0);
            $table->string('status')->default('active');
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.teachers', 'shearerline_teachers'));
    }
};
