<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreTeacherRequest;
use Shearerline\Http\Requests\UpdateTeacherRequest;
use Shearerline\Models\Teacher;

class TeacherController extends BaseController
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'status', 'per_page']);
        $teachers = Shearerline::getTeachers($filters);

        return response()->json([
            'success' => true,
            'data' => $teachers,
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $teacher = Shearerline::createTeacher($request->validated());

        return response()->json([
            'success' => true,
            'data' => $teacher,
            'message' => '教师创建成功',
        ], 201);
    }

    public function show(Teacher $teacher)
    {
        $teacher = Shearerline::getTeacher($teacher->id);

        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher = Shearerline::updateTeacher($teacher->id, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $teacher,
            'message' => '教师更新成功',
        ]);
    }

    public function destroy(Teacher $teacher)
    {
        Shearerline::deleteTeacher($teacher->id);

        return response()->json([
            'success' => true,
            'message' => '教师删除成功',
        ]);
    }
}
