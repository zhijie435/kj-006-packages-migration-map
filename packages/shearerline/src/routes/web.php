<?php

use Illuminate\Support\Facades\Route;
use Shearerline\Http\Controllers\CourseController;
use Shearerline\Http\Controllers\LessonController;
use Shearerline\Http\Controllers\StudentController;
use Shearerline\Http\Controllers\TeacherController;
use Shearerline\Http\Controllers\EnrollmentController;
use Shearerline\Http\Controllers\DashboardController;

Route::group([
    'prefix' => config('shearerline.route_prefix', 'shearerline'),
    'middleware' => config('shearerline.route_middleware', ['web']),
    'namespace' => 'Shearerline\Http\Controllers',
], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('shearerline.dashboard');
    Route::get('/statistics', [DashboardController::class, 'statistics'])->name('shearerline.statistics');

    Route::resource('courses', CourseController::class)->names([
        'index' => 'shearerline.courses.index',
        'create' => 'shearerline.courses.create',
        'store' => 'shearerline.courses.store',
        'show' => 'shearerline.courses.show',
        'edit' => 'shearerline.courses.edit',
        'update' => 'shearerline.courses.update',
        'destroy' => 'shearerline.courses.destroy',
    ]);

    Route::resource('courses.lessons', LessonController::class)->names([
        'index' => 'shearerline.courses.lessons.index',
        'create' => 'shearerline.courses.lessons.create',
        'store' => 'shearerline.courses.lessons.store',
        'show' => 'shearerline.courses.lessons.show',
        'edit' => 'shearerline.courses.lessons.edit',
        'update' => 'shearerline.courses.lessons.update',
        'destroy' => 'shearerline.courses.lessons.destroy',
    ]);

    Route::resource('students', StudentController::class)->names([
        'index' => 'shearerline.students.index',
        'create' => 'shearerline.students.create',
        'store' => 'shearerline.students.store',
        'show' => 'shearerline.students.show',
        'edit' => 'shearerline.students.edit',
        'update' => 'shearerline.students.update',
        'destroy' => 'shearerline.students.destroy',
    ]);

    Route::resource('teachers', TeacherController::class)->names([
        'index' => 'shearerline.teachers.index',
        'create' => 'shearerline.teachers.create',
        'store' => 'shearerline.teachers.store',
        'show' => 'shearerline.teachers.show',
        'edit' => 'shearerline.teachers.edit',
        'update' => 'shearerline.teachers.update',
        'destroy' => 'shearerline.teachers.destroy',
    ]);

    Route::resource('enrollments', EnrollmentController::class)->names([
        'index' => 'shearerline.enrollments.index',
        'create' => 'shearerline.enrollments.create',
        'store' => 'shearerline.enrollments.store',
        'show' => 'shearerline.enrollments.show',
        'edit' => 'shearerline.enrollments.edit',
        'update' => 'shearerline.enrollments.update',
        'destroy' => 'shearerline.enrollments.destroy',
    ]);

    Route::post('enrollments/{enrollment}/cancel', [EnrollmentController::class, 'cancel'])->name('shearerline.enrollments.cancel');
});
