<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Models\Course;
use Shearerline\Models\Student;
use Shearerline\Models\Teacher;
use Shearerline\Models\Enrollment;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $statistics = $this->getStatistics();

        if (view()->exists('shearerline::dashboard')) {
            return view('shearerline::dashboard', compact('statistics'));
        }

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    public function statistics(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->getStatistics(),
        ]);
    }

    protected function getStatistics(): array
    {
        return [
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
                ->sum(config('shearerline.tables.courses', 'shearerline_courses') . '.price'),
            'recent_courses' => Course::latest()->take(5)->get(),
            'recent_students' => Student::latest()->take(5)->get(),
            'recent_enrollments' => Enrollment::with(['course', 'student'])->latest()->take(5)->get(),
        ];
    }
}
