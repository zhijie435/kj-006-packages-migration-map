<?php

namespace Shearerline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('teacher') ?? $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:' . config('shearerline.tables.teachers', 'shearerline_teachers') . ',email,' . $teacherId,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|string|url',
            'title' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'specialties' => 'nullable|array',
            'experience_years' => 'nullable|integer|min:0',
            'status' => 'nullable|in:active,inactive,suspended',
            'remark' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '教师姓名不能为空',
            'name.max' => '教师姓名不能超过100个字符',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '该邮箱已被注册',
            'experience_years.integer' => '教学年限必须是整数',
            'experience_years.min' => '教学年限不能为负数',
            'status.in' => '状态不正确',
        ];
    }
}
