<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreCourseRequest;
use Shearerline\Http\Requests\UpdateCourseRequest;
use Shearerline\Models\Course;
use Shearerline\Models\Teacher;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'teacher_id', 'status']);
        $courses = Shearerline::getCourses($filters);
        $teachers = Teacher::where('status', 'active')->pluck('name', 'id');

        if (view()->exists('shearerline::courses.index')) {
            return view('shearerline::courses.index', compact('courses', 'teachers', 'filters'));
        }

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    public function create()
    {
        $teachers = Teacher::where('status', 'active')->pluck('name', 'id');

        if (view()->exists('shearerline::courses.create')) {
            return view('shearerline::courses.create', compact('teachers'));
        }

        return response()->json([
            'success' => true,
            'data' => ['teachers' => $teachers],
        ]);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Shearerline::createCourse($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $course,
                'message' => '课程创建成功',
            ], 201);
        }

        return redirect()->route('shearerline.courses.show', $course)
            ->with('success', '课程创建成功');
    }

    public function show(Course $course)
    {
        $course = Shearerline::getCourse($course->id);

        if (view()->exists('shearerline::courses.show')) {
            return view('shearerline::courses.show', compact('course'));
        }

        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }

    public function edit(Course $course)
    {
        $teachers = Teacher::where('status', 'active')->pluck('name', 'id');

        if (view()->exists('shearerline::courses.edit')) {
            return view('shearerline::courses.edit', compact('course', 'teachers'));
        }

        return response()->json([
            'success' => true,
            'data' => ['course' => $course, 'teachers' => $teachers],
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course = Shearerline::updateCourse($course->id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $course,
                'message' => '课程更新成功',
            ]);
        }

        return redirect()->route('shearerline.courses.show', $course)
            ->with('success', '课程更新成功');
    }

    public function destroy(Request $request, Course $course)
    {
        Shearerline::deleteCourse($course->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '课程删除成功',
            ]);
        }

        return redirect()->route('shearerline.courses.index')
            ->with('success', '课程删除成功');
    }
}
