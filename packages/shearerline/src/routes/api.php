<?php

use Illuminate\Support\Facades\Route;
use Shearerline\Http\Controllers\Api\CourseController as ApiCourseController;
use Shearerline\Http\Controllers\Api\LessonController as ApiLessonController;
use Shearerline\Http\Controllers\Api\StudentController as ApiStudentController;
use Shearerline\Http\Controllers\Api\TeacherController as ApiTeacherController;
use Shearerline\Http\Controllers\Api\EnrollmentController as ApiEnrollmentController;
use Shearerline\Http\Controllers\Api\DashboardController as ApiDashboardController;
use Shearerline\Http\Controllers\Api\SupplierController as ApiSupplierController;
use Shearerline\Http\Controllers\Api\ProductController as ApiProductController;
use Shearerline\Http\Controllers\Api\MoqOrderController as ApiMoqOrderController;
use Shearerline\Http\Controllers\Api\ShipmentController as ApiShipmentController;

Route::group([
    'prefix' => config('shearerline.api_route_prefix', 'api/shearerline'),
    'middleware' => config('shearerline.api_middleware', ['api']),
    'namespace' => 'Shearerline\Http\Controllers\Api',
], function () {
    Route::get('statistics', [ApiDashboardController::class, 'statistics'])->name('shearerline.api.statistics');

    Route::get('courses', [ApiCourseController::class, 'index'])->name('shearerline.api.courses.index');
    Route::post('courses', [ApiCourseController::class, 'store'])->name('shearerline.api.courses.store');
    Route::get('courses/{course}', [ApiCourseController::class, 'show'])->name('shearerline.api.courses.show');
    Route::put('courses/{course}', [ApiCourseController::class, 'update'])->name('shearerline.api.courses.update');
    Route::delete('courses/{course}', [ApiCourseController::class, 'destroy'])->name('shearerline.api.courses.destroy');
    Route::get('courses/{course}/statistics', [ApiCourseController::class, 'statistics'])->name('shearerline.api.courses.statistics');

    Route::get('courses/{course}/lessons', [ApiLessonController::class, 'index'])->name('shearerline.api.courses.lessons.index');
    Route::post('courses/{course}/lessons', [ApiLessonController::class, 'store'])->name('shearerline.api.courses.lessons.store');
    Route::get('lessons/{lesson}', [ApiLessonController::class, 'show'])->name('shearerline.api.lessons.show');
    Route::put('lessons/{lesson}', [ApiLessonController::class, 'update'])->name('shearerline.api.lessons.update');
    Route::delete('lessons/{lesson}', [ApiLessonController::class, 'destroy'])->name('shearerline.api.lessons.destroy');

    Route::get('students', [ApiStudentController::class, 'index'])->name('shearerline.api.students.index');
    Route::post('students', [ApiStudentController::class, 'store'])->name('shearerline.api.students.store');
    Route::get('students/{student}', [ApiStudentController::class, 'show'])->name('shearerline.api.students.show');
    Route::put('students/{student}', [ApiStudentController::class, 'update'])->name('shearerline.api.students.update');
    Route::delete('students/{student}', [ApiStudentController::class, 'destroy'])->name('shearerline.api.students.destroy');

    Route::get('teachers', [ApiTeacherController::class, 'index'])->name('shearerline.api.teachers.index');
    Route::post('teachers', [ApiTeacherController::class, 'store'])->name('shearerline.api.teachers.store');
    Route::get('teachers/{teacher}', [ApiTeacherController::class, 'show'])->name('shearerline.api.teachers.show');
    Route::put('teachers/{teacher}', [ApiTeacherController::class, 'update'])->name('shearerline.api.teachers.update');
    Route::delete('teachers/{teacher}', [ApiTeacherController::class, 'destroy'])->name('shearerline.api.teachers.destroy');

    Route::get('enrollments', [ApiEnrollmentController::class, 'index'])->name('shearerline.api.enrollments.index');
    Route::post('enrollments', [ApiEnrollmentController::class, 'store'])->name('shearerline.api.enrollments.store');
    Route::get('enrollments/{enrollment}', [ApiEnrollmentController::class, 'show'])->name('shearerline.api.enrollments.show');
    Route::put('enrollments/{enrollment}', [ApiEnrollmentController::class, 'update'])->name('shearerline.api.enrollments.update');
    Route::delete('enrollments/{enrollment}', [ApiEnrollmentController::class, 'destroy'])->name('shearerline.api.enrollments.destroy');
    Route::post('enrollments/{enrollment}/cancel', [ApiEnrollmentController::class, 'cancel'])->name('shearerline.api.enrollments.cancel');

    Route::get('suppliers/all', [ApiSupplierController::class, 'all'])->name('shearerline.api.suppliers.all');
    Route::apiResource('suppliers', ApiSupplierController::class, ['names' => 'shearerline.api.suppliers']);

    Route::get('products/by-supplier/{supplierId}', [ApiProductController::class, 'bySupplier'])->name('shearerline.api.products.bySupplier');
    Route::apiResource('products', ApiProductController::class, ['names' => 'shearerline.api.products']);

    Route::get('moq-orders/statistics', [ApiMoqOrderController::class, 'statistics'])->name('shearerline.api.moq-orders.statistics');
    Route::post('moq-orders/{order}/confirm', [ApiMoqOrderController::class, 'confirm'])->name('shearerline.api.moq-orders.confirm');
    Route::post('moq-orders/{order}/process', [ApiMoqOrderController::class, 'process'])->name('shearerline.api.moq-orders.process');
    Route::post('moq-orders/{order}/ship', [ApiMoqOrderController::class, 'ship'])->name('shearerline.api.moq-orders.ship');
    Route::post('moq-orders/{order}/complete', [ApiMoqOrderController::class, 'complete'])->name('shearerline.api.moq-orders.complete');
    Route::post('moq-orders/{order}/cancel', [ApiMoqOrderController::class, 'cancel'])->name('shearerline.api.moq-orders.cancel');
    Route::post('moq-orders/{order}/refund', [ApiMoqOrderController::class, 'refund'])->name('shearerline.api.moq-orders.refund');
    Route::post('moq-orders/{order}/pay', [ApiMoqOrderController::class, 'pay'])->name('shearerline.api.moq-orders.pay');
    Route::apiResource('moq-orders', ApiMoqOrderController::class, ['names' => 'shearerline.api.moq-orders']);

    Route::get('shipments/by-order/{orderId}', [ApiShipmentController::class, 'byOrder'])->name('shearerline.api.shipments.byOrder');
    Route::post('shipments/{shipment}/update-tracking', [ApiShipmentController::class, 'updateTracking'])->name('shearerline.api.shipments.updateTracking');
    Route::apiResource('shipments', ApiShipmentController::class, ['names' => 'shearerline.api.shipments']);
});
