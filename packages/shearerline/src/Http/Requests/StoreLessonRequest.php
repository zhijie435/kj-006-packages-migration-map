<?php

namespace Shearerline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id' => 'required|exists:' . config('shearerline.tables.courses', 'shearerline_courses') . ',id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string|url',
            'duration' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer|min:0',
            'is_free' => 'nullable|boolean',
            'status' => 'nullable|in:draft,published,archived',
        ];
    }

    public function messages(): array
    {
        return [
            'course_id.required' => '请选择所属课程',
            'course_id.exists' => '所选课程不存在',
            'title.required' => '课时标题不能为空',
            'title.max' => '课时标题不能超过255个字符',
            'video_url.url' => '视频地址格式不正确',
            'status.in' => '状态不正确',
        ];
    }
}
