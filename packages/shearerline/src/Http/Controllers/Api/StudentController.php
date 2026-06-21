<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreStudentRequest;
use Shearerline\Http\Requests\UpdateStudentRequest;
use Shearerline\Models\Student;

class StudentController extends BaseController
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'status', 'per_page']);
        $students = Shearerline::getStudents($filters);

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Shearerline::createStudent($request->validated());

        return response()->json([
            'success' => true,
            'data' => $student,
            'message' => '学生创建成功',
        ], 201);
    }

    public function show(Student $student)
    {
        $student = Shearerline::getStudent($student->id);

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student = Shearerline::updateStudent($student->id, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $student,
            'message' => '学生更新成功',
        ]);
    }

    public function destroy(Student $student)
    {
        Shearerline::deleteStudent($student->id);

        return response()->json([
            'success' => true,
            'message' => '学生删除成功',
        ]);
    }
}
