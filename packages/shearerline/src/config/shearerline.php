<?php

return [

    'name' => env('SHEARERLINE_NAME', 'Shearerline'),

    'route_prefix' => env('SHEARERLINE_ROUTE_PREFIX', 'shearerline'),

    'route_middleware' => ['web'],

    'api_route_prefix' => env('SHEARERLINE_API_PREFIX', 'api/shearerline'),

    'api_middleware' => ['api'],

    'pagination' => [
        'per_page' => env('SHEARERLINE_PER_PAGE', 15),
    ],

    'models' => [
        'course' => \Shearerline\Models\Course::class,
        'lesson' => \Shearerline\Models\Lesson::class,
        'student' => \Shearerline\Models\Student::class,
        'teacher' => \Shearerline\Models\Teacher::class,
        'enrollment' => \Shearerline\Models\Enrollment::class,
        'supplier' => \Shearerline\Models\Supplier::class,
        'product' => \Shearerline\Models\Product::class,
        'moq_order' => \Shearerline\Models\MoqOrder::class,
        'moq_order_item' => \Shearerline\Models\MoqOrderItem::class,
        'shipment' => \Shearerline\Models\Shipment::class,
    ],

    'tables' => [
        'courses' => 'shearerline_courses',
        'lessons' => 'shearerline_lessons',
        'students' => 'shearerline_students',
        'teachers' => 'shearerline_teachers',
        'enrollments' => 'shearerline_enrollments',
        'suppliers' => 'shearerline_suppliers',
        'products' => 'shearerline_products',
        'moq_orders' => 'shearerline_moq_orders',
        'moq_order_items' => 'shearerline_moq_order_items',
        'shipments' => 'shearerline_shipments',
    ],

    'upload' => [
        'disk' => env('SHEARERLINE_DISK', 'public'),
        'directory' => 'shearerline',
    ],

    'views' => [
        'enabled' => true,
        'prefix' => 'shearerline',
    ],

];
