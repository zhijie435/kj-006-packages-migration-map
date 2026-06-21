<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreLessonRequest;
use Shearerline\Http\Requests\UpdateLessonRequest;
use Shearerline\Models\Course;
use Shearerline\Models\Lesson;

class LessonController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $filters = $request->only(['keyword', 'status']);
        $lessons = Shearerline::getLessons($course->id, $filters);

        if (view()->exists('shearerline::lessons.index')) {
            return view('shearerline::lessons.index', compact('course', 'lessons', 'filters'));
        }

        return response()->json([
            'success' => true,
            'data' => $lessons,
        ]);
    }

    public function create(Course $course)
    {
        if (view()->exists('shearerline::lessons.create')) {
            return view('shearerline::lessons.create', compact('course'));
        }

        return response()->json([
            'success' => true,
            'data' => ['course' => $course],
        ]);
    }

    public function store(StoreLessonRequest $request, Course $course)
    {
        $data = array_merge($request->validated(), ['course_id' => $course->id]);
        $lesson = Shearerline::createLesson($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $lesson,
                'message' => '课时创建成功',
            ], 201);
        }

        return redirect()->route('shearerline.courses.lessons.show', [$course, $lesson])
            ->with('success', '课时创建成功');
    }

    public function show(Course $course, Lesson $lesson)
    {
        $lesson = Shearerline::getLesson($lesson->id);

        if (view()->exists('shearerline::lessons.show')) {
            return view('shearerline::lessons.show', compact('course', 'lesson'));
        }

        return response()->json([
            'success' => true,
            'data' => $lesson,
        ]);
    }

    public function edit(Course $course, Lesson $lesson)
    {
        if (view()->exists('shearerline::lessons.edit')) {
            return view('shearerline::lessons.edit', compact('course', 'lesson'));
        }

        return response()->json([
            'success' => true,
            'data' => ['course' => $course, 'lesson' => $lesson],
        ]);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson)
    {
        $lesson = Shearerline::updateLesson($lesson->id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $lesson,
                'message' => '课时更新成功',
            ]);
        }

        return redirect()->route('shearerline.courses.lessons.show', [$course, $lesson])
            ->with('success', '课时更新成功');
    }

    public function destroy(Request $request, Course $course, Lesson $lesson)
    {
        Shearerline::deleteLesson($lesson->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '课时删除成功',
            ]);
        }

        return redirect()->route('shearerline.courses.lessons.index', $course)
            ->with('success', '课时删除成功');
    }
}
