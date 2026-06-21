<?php

namespace Shearerline\Http\Controllers;

use Illuminate\Http\Request;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreStudentRequest;
use Shearerline\Http\Requests\UpdateStudentRequest;
use Shearerline\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['keyword', 'status']);
        $students = Shearerline::getStudents($filters);

        if (view()->exists('shearerline::students.index')) {
            return view('shearerline::students.index', compact('students', 'filters'));
        }

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function create()
    {
        if (view()->exists('shearerline::students.create')) {
            return view('shearerline::students.create');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Shearerline::createStudent($request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $student,
                'message' => '学生创建成功',
            ], 201);
        }

        return redirect()->route('shearerline.students.show', $student)
            ->with('success', '学生创建成功');
    }

    public function show(Student $student)
    {
        $student = Shearerline::getStudent($student->id);

        if (view()->exists('shearerline::students.show')) {
            return view('shearerline::students.show', compact('student'));
        }

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function edit(Student $student)
    {
        if (view()->exists('shearerline::students.edit')) {
            return view('shearerline::students.edit', compact('student'));
        }

        return response()->json([
            'success' => true,
            'data' => $student,
        ]);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student = Shearerline::updateStudent($student->id, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $student,
                'message' => '学生更新成功',
            ]);
        }

        return redirect()->route('shearerline.students.show', $student)
            ->with('success', '学生更新成功');
    }

    public function destroy(Request $request, Student $student)
    {
        Shearerline::deleteStudent($student->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '学生删除成功',
            ]);
        }

        return redirect()->route('shearerline.students.index')
            ->with('success', '学生删除成功');
    }
}
