<?php

namespace Shearerline\Http\Controllers\Api;

use Illuminate\Http\Request;
use Shearerline\Http\Controllers\Controller as BaseController;
use Shearerline\Facades\Shearerline;
use Shearerline\Http\Requests\StoreEnrollmentRequest;
use Shearerline\Http\Requests\UpdateEnrollmentRequest;
use Shearerline\Models\Enrollment;

class EnrollmentController extends BaseController
{
    public function index(Request $request)
    {
        $filters = $request->only(['course_id', 'student_id', 'status', 'per_page']);
        $enrollments = Shearerline::getEnrollments($filters);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);
    }

    public function store(StoreEnrollmentRequest $request)
    {
        $enrollment = Shearerline::enrollStudent($request->validated());

        return response()->json([
            'success' => true,
            'data' => $enrollment,
            'message' => '报名成功',
        ], 201);
    }

    public function show(Enrollment $enrollment)
    {
        return response()->json([
            'success' => true,
            'data' => $enrollment->load(['course', 'student']),
        ]);
    }

    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment)
    {
        $enrollment->update($request->validated());

        return response()->json([
            'success' => true,
            'data' => $enrollment->fresh(),
            'message' => '报名信息更新成功',
        ]);
    }

    public function destroy(Enrollment $enrollment)
    {
        Shearerline::cancelEnrollment($enrollment->id);

        return response()->json([
            'success' => true,
            'message' => '报名已删除',
        ]);
    }

    public function cancel(Enrollment $enrollment)
    {
        $enrollment->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'data' => $enrollment->fresh(),
            'message' => '报名已取消',
        ]);
    }
}
