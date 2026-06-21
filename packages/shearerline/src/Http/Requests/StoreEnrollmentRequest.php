<?php

namespace Shearerline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:' . config('shearerline.tables.courses', 'shearerline_courses') . ',id',
            'student_id' => 'required|exists:' . config('shearerline.tables.students', 'shearerline_students') . ',id',
            'enrolled_at' => 'nullable|date',
            'status' => 'nullable|in:enrolled,completed,cancelled,refunded',
            'progress' => 'nullable|numeric|min:0|max:100',
            'remark' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => '请选择课程',
            'course_id.exists' => '所选课程不存在',
            'student_id.required' => '请选择学生',
            'student_id.exists' => '所选学生不存在',
            'status.in' => '状态不正确',
            'progress.numeric' => '进度必须是数字',
            'progress.min' => '进度不能小于0',
            'progress.max' => '进度不能超过100',
        ];
    }
}
