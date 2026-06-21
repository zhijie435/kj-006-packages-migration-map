<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreCourseRequest;
use Shearerline\Http\Requests\UpdateCourseRequest;
use Shearerline\Models\Course;

class CourseController extends BaseController
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'teacher_id', 'status', 'per_page']);
        $courses = Shearerline::getCourses($filters);

        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = Shearerline::createCourse($request->validated());

        return response()->json([
            'success' => true,
            'data' => $course,
            'message' => '课程创建成功',
        ], 201);
    }

    public function show(Course $course)
    {
        $course = Shearerline::getCourse($course->id);

        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }

    public function update(UpdateCourseRequest $request, Course $course)
    {
        $course = Shearerline::updateCourse($course->id, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $course,
            'message' => '课程更新成功',
        ]);
    }

    public function destroy(Course $course)
    {
        Shearerline::deleteCourse($course->id);

        return response()->json([
            'success' => true,
            'message' => '课程删除成功',
        ]);
    }

    public function statistics(Course $course)
    {
        return response()->json([
            'success' => true,
            'data' => Shearerline::getCourseStatistics($course->id),
        ]);
    }
}
