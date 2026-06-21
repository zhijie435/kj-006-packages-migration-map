<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreTeacherRequest;
use Shearerline\Http\Requests\UpdateTeacherRequest;
use Shearerline\Models\Teacher;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'status']);
        $teachers = Shearerline::getTeachers($filters);

        if (view()->exists('shearerline::teachers.index')) {
            return view('shearerline::teachers.index', compact('teachers', 'filters'));
        }

        return response()->json([
            'success' => true,
            'data' => $teachers,
        ]);
    }

    public function create()
    {
        if (view()->exists('shearerline::teachers.create')) {
            return view('shearerline::teachers.create');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $teacher = Shearerline::createTeacher($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $teacher,
                'message' => '教师创建成功',
            ], 201);
        }

        return redirect()->route('shearerline.teachers.show', $teacher)
            ->with('success', '教师创建成功');
    }

    public function show(Teacher $teacher)
    {
        $teacher = Shearerline::getTeacher($teacher->id);

        if (view()->exists('shearerline::teachers.show')) {
            return view('shearerline::teachers.show', compact('teacher'));
        }

        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }

    public function edit(Teacher $teacher)
    {
        if (view()->exists('shearerline::teachers.edit')) {
            return view('shearerline::teachers.edit', compact('teacher'));
        }

        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher = Shearerline::updateTeacher($teacher->id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $teacher,
                'message' => '教师更新成功',
            ]);
        }

        return redirect()->route('shearerline.teachers.show', $teacher)
            ->with('success', '教师更新成功');
    }

    public function destroy(Request $request, Teacher $teacher)
    {
        Shearerline::deleteTeacher($teacher->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '教师删除成功',
            ]);
        }

        return redirect()->route('shearerline.teachers.index')
            ->with('success', '教师删除成功');
    }
}
