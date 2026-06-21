<?php

if (!function_exists('shearerline')) {
    function shearerline(): \Shearerline\Shearerline
    {
        return app('shearerline');
    }
}

if (!function_exists('format_price')) {
    function format_price(float $amount, string $currency = 'CNY'): string
    {
        $symbols = [
            'CNY' => '¥',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
        ];
        $symbol = $symbols[$currency] ?? '';
        return $symbol . number_format($amount, 2);
    }
}

if (!function_exists('format_duration')) {
    function format_duration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        if ($hours > 0) {
            return "{$hours}小时{$mins}分钟";
        }
        return "{$mins}分钟";
    }
}

if (!function_exists('course_status_text')) {
    function course_status_text(string $status): string
    {
        return [
            'draft' => '草稿',
            'published' => '已发布',
            'archived' => '已归档',
        ][$status] ?? '未知';
    }
}

if (!function_exists('enrollment_status_text')) {
    function enrollment_status_text(string $status): string
    {
        return [
            'enrolled' => '已报名',
            'completed' => '已完成',
            'cancelled' => '已取消',
            'refunded' => '已退款',
        ][$status] ?? '未知';
    }
}

if (!function_exists('user_status_text')) {
    function user_status_text(string $status): string
    {
        return [
            'active' => '正常',
            'inactive' => '未激活',
            'suspended' => '已停用',
        ][$status] ?? '未知';
    }
}

if (!function_exists('difficulty_text')) {
    function difficulty_text(string $difficulty): string
    {
        return [
            'beginner' => '入门',
            'intermediate' => '中级',
            'advanced' => '高级',
        ][$difficulty] ?? '未知';
    }
}
