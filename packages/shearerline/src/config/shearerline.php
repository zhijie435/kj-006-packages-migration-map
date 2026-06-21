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

    'status' => [
        'moq_order' => [
            'pending' => '待确认',
            'confirmed' => '已确认',
            'processing' => '处理中',
            'shipped' => '已发货',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'refunded' => '已退款',
        ],
        'shipment' => [
            'pending' => '待发货',
            'shipped' => '已发货',
            'in_transit' => '运输中',
            'delivered' => '已签收',
            'returned' => '已退回',
            'failed' => '发货失败',
        ],
    ],

    'views' => [
        'enabled' => true,
        'prefix' => 'shearerline',
    ],

];
