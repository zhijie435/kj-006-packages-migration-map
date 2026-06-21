<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.lessons', 'shearerline_lessons'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained(config('shearerline.tables.courses', 'shearerline_courses'))->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedInteger('duration')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_free')->default(false);
            $table->string('status')->default('draft');
            $table->timestamps();

            $table->index('course_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.lessons', 'shearerline_lessons'));
    }
};
