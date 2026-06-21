<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreLessonRequest;
use Shearerline\Http\Requests\UpdateLessonRequest;
use Shearerline\Models\Course;
use Shearerline\Models\Lesson;

class LessonController extends BaseController
{
    public function index(Request $request, Course $course)
    {
        $filters = $request->only(['keyword', 'status', 'per_page']);
        $lessons = Shearerline::getLessons($course->id, $filters);

        return response()->json([
            'success' => true,
            'data' => $lessons,
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $data = array_merge($request->validated(), ['course_id' => $course->id]);
        $lesson = Shearerline::createLesson($data);

        return response()->json([
            'success' => true,
            'data' => $lesson,
            'message' => '课时创建成功',
        ], 201);
    }

    public function show(Lesson $lesson)
    {
        $lesson = Shearerline::getLesson($lesson->id);

        return response()->json([
            'success' => true,
            'data' => $lesson,
        ]);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $lesson = Shearerline::updateLesson($lesson->id, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $lesson,
            'message' => '课时更新成功',
        ]);
    }

    public function destroy(Lesson $lesson)
    {
        Shearerline::deleteLesson($lesson->id);

        return response()->json([
            'success' => true,
            'message' => '课时删除成功',
        ]);
    }
}
