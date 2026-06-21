<?php

namespace Shearerline\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Shearerline\Models\Course;
use Shearerline\Models\Enrollment;
use Shearerline\Models\Lesson;
use Shearerline\Models\Product;
use Shearerline\Models\Student;
use Shearerline\Models\Supplier;
use Shearerline\Models\Teacher;

class ShearerlineDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            'shipments',
            'moq_order_items',
            'moq_orders',
            'products',
            'suppliers',
            'enrollments',
            'lessons',
            'courses',
            'students',
            'teachers',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $key) {
            DB::table(config('shearerline.tables.' . $key, 'shearerline_' . $key))->truncate();
        }

        Schema::enableForeignKeyConstraints();

        $teacher = Teacher::create([
            'name' => '张明',
            'email' => 'zhangming@shearerline.com',
            'phone' => '13800000001',
            'title' => '高级讲师',
            'bio' => '拥有十年后端开发教学经验的资深讲师。',
            'specialties' => ['Laravel', 'PHP', '后端架构'],
            'experience_years' => 10,
            'status' => 'active',
        ]);

        Teacher::create([
            'name' => '李华',
            'email' => 'lihua@shearerline.com',
            'phone' => '13800000002',
            'title' => '讲师',
            'bio' => '专注于前端开发与工程化教学。',
            'specialties' => ['Vue', 'React', '前端工程'],
            'experience_years' => 5,
            'status' => 'active',
        ]);

        $student = Student::create([
            'name' => '王小杰',
            'email' => 'wangxiaojie@student.com',
            'phone' => '13900000001',
            'gender' => 'male',
            'birth_date' => '2000-01-15',
            'address' => '北京市海淀区',
            'status' => 'active',
        ]);

        Student::create([
            'name' => '赵小琳',
            'email' => 'zhaoxiaolin@student.com',
            'phone' => '13900000002',
            'gender' => 'female',
            'birth_date' => '2001-05-20',
            'address' => '上海市浦东新区',
            'status' => 'active',
        ]);

        $course = Course::create([
            'title' => 'Laravel 实战进阶',
            'description' => '从入门到精通 Laravel 框架的进阶课程。',
            'teacher_id' => $teacher->id,
            'price' => 299.00,
            'duration' => 600,
            'difficulty' => 'intermediate',
            'category' => 'backend',
            'tags' => ['Laravel', 'PHP', '后端'],
            'status' => 'published',
            'start_date' => '2024-02-01',
            'end_date' => '2024-06-30',
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'title' => '课程导论',
            'description' => '本节介绍课程整体安排与学习目标。',
            'content' => '欢迎来到 Laravel 实战进阶课程，本节将概述课程内容。',
            'video_url' => 'https://example.com/videos/lesson-1.mp4',
            'duration' => 30,
            'sort_order' => 1,
            'is_free' => true,
            'status' => 'published',
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'title' => '路由与控制器',
            'description' => '深入讲解 Laravel 路由与控制器。',
            'content' => '本节我们学习 Laravel 的路由定义与控制器组织方式。',
            'video_url' => 'https://example.com/videos/lesson-2.mp4',
            'duration' => 45,
            'sort_order' => 2,
            'is_free' => false,
            'status' => 'published',
        ]);

        Lesson::create([
            'course_id' => $course->id,
            'title' => 'Eloquent ORM',
            'description' => '掌握 Eloquent ORM 的核心用法。',
            'content' => '本节我们学习 Eloquent 模型、关联与查询构造器。',
            'video_url' => 'https://example.com/videos/lesson-3.mp4',
            'duration' => 60,
            'sort_order' => 3,
            'is_free' => false,
            'status' => 'draft',
        ]);

        Enrollment::create([
            'course_id' => $course->id,
            'student_id' => $student->id,
            'enrolled_at' => now(),
            'status' => 'enrolled',
            'progress' => 25.00,
        ]);

        $supplier = Supplier::create([
            'name' => '优质文具供应商',
            'code' => 'SUP-001',
            'contact_name' => '陈经理',
            'contact_phone' => '13700000001',
            'address' => '广州市天河区',
            'remark' => '长期合作供应商。',
            'is_active' => true,
        ]);

        Product::create([
            'supplier_id' => $supplier->id,
            'name' => '高端笔记本套装',
            'sku' => 'PRD-001',
            'description' => '包含笔记本、签字笔与文件夹的精美套装。',
            'price' => 128.00,
            'moq' => 10,
            'stock' => 500,
            'unit' => '套',
            'image_url' => 'https://example.com/images/product-1.png',
            'specs' => ['材质' => '真皮', '颜色' => '棕色'],
            'is_active' => true,
        ]);
    }
}
