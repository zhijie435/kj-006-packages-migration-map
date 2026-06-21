<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Facades\Shearerline;
use Shearerline\Models\Course;
use Shearerline\Models\Student;
use Shearerline\Models\Teacher;
use Shearerline\Models\Enrollment;

class DashboardController extends BaseController
{
    public function statistics(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'courses_count' => Course::count(),
                'published_courses_count' => Course::where('status', 'published')->count(),
                'students_count' => Student::count(),
                'active_students_count' => Student::where('status', 'active')->count(),
                'teachers_count' => Teacher::count(),
                'active_teachers_count' => Teacher::where('status', 'active')->count(),
                'enrollments_count' => Enrollment::count(),
                'completed_enrollments_count' => Enrollment::where('status', 'completed')->count(),
                'total_revenue' => Enrollment::where('status', '!=', 'refunded')
                    ->join(config('shearerline.tables.courses', 'shearerline_courses'), config('shearerline.tables.enrollments', 'shearerline_enrollments') . '.course_id', '=', config('shearerline.tables.courses', 'shearerline_courses') . '.id')
                    ->sum(config('shearerline.tables.courses', 'shearerline_courses') . '.price') ?? 0,
            ],
        ]);
    }
}
