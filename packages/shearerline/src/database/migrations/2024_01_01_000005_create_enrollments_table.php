<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('shearerline.tables.enrollments', 'shearerline_enrollments'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained(config('shearerline.tables.courses', 'shearerline_courses'))->cascadeOnDelete();
            $table->foreignId('student_id')->constrained(config('shearerline.tables.students', 'shearerline_students'))->cascadeOnDelete();
            $table->dateTime('enrolled_at')->nullable();
            $table->string('status')->default('enrolled');
            $table->decimal('progress', 5, 2)->default(0);
            $table->dateTime('completed_at')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'student_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('shearerline.tables.enrollments', 'shearerline_enrollments'));
    }
};
