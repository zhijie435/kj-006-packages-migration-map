<?php

namespace Shearerline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'sometimes|required|exists:' . config('shearerline.tables.teachers', 'shearerline_teachers') . ',id',
            'cover_image' => 'nullable|string|url',
            'price' => 'nullable|numeric|min:0',
            'duration' => 'nullable|integer|min:0',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'category' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'status' => 'nullable|in:draft,published,archived',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => '课程标题不能为空',
            'title.max' => '课程标题不能超过255个字符',
            'teacher_id.required' => '请选择授课教师',
            'teacher_id.exists' => '所选教师不存在',
            'price.numeric' => '价格必须是数字',
            'price.min' => '价格不能为负数',
            'difficulty.in' => '难度级别不正确',
            'status.in' => '状态不正确',
            'end_date.after_or_equal' => '结束日期必须大于或等于开始日期',
        ];
    }
}
