<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreEnrollmentRequest;
use Shearerline\Http\Requests\UpdateEnrollmentRequest;
use Shearerline\Models\Course;
use Shearerline\Models\Enrollment;
use Shearerline\Models\Student;

class EnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['course_id', 'student_id', 'status']);
        $enrollments = Shearerline::getEnrollments($filters);
        $courses = Course::where('status', 'published')->pluck('title', 'id');
        $students = Student::where('status', 'active')->pluck('name', 'id');

        if (view()->exists('shearerline::enrollments.index')) {
            return view('shearerline::enrollments.index', compact('enrollments', 'courses', 'students', 'filters'));
        }

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);
    }

    public function create()
    {
        $courses = Course::where('status', 'published')->pluck('title', 'id');
        $students = Student::where('status', 'active')->pluck('name', 'id');

        if (view()->exists('shearerline::enrollments.create')) {
            return view('shearerline::enrollments.create', compact('courses', 'students'));
        }

        return response()->json([
            'success' => true,
            'data' => ['courses' => $courses, 'students' => $students],
        ]);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Shearerline::enrollStudent($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $enrollment,
                'message' => '报名成功',
            ], 201);
        }

        return redirect()->route('shearerline.enrollments.show', $enrollment)
            ->with('success', '报名成功');
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment = Shearerline::getEnrollments(['enrollment_id' => $enrollment->id])->first();

        if (view()->exists('shearerline::enrollments.show')) {
            return view('shearerline::enrollments.show', compact('enrollment'));
        }

        return response()->json([
            'success' => true,
            'data' => $enrollment,
        ]);
    }

    public function edit(Enrollment $enrollment)
    {
        if (view()->exists('shearerline::enrollments.edit')) {
            return view('shearerline::enrollments.edit', compact('enrollment'));
        }

        return response()->json([
            'success' => true,
            'data' => $enrollment,
        ]);
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $enrollment->fresh(),
                'message' => '报名信息更新成功',
            ]);
        }

        return redirect()->route('shearerline.enrollments.show', $enrollment)
            ->with('success', '报名信息更新成功');
    }

    public function destroy(Request $request, Enrollment $enrollment)
    {
        Shearerline::cancelEnrollment($enrollment->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '报名已取消',
            ]);
        }

        return redirect()->route('shearerline.enrollments.index')
            ->with('success', '报名已取消');
    }

    public function cancel(Request $request, Enrollment $enrollment)
    {
        $enrollment->update(['status' => 'cancelled']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $enrollment->fresh(),
                'message' => '报名已取消',
            ]);
        }

        return redirect()->back()->with('success', '报名已取消');
    }
}
