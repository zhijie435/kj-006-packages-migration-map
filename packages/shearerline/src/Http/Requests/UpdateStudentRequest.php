<?php

namespace Shearerline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student') ?? $this->route('id');

        return [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:' . config('shearerline.tables.students', 'shearerline_students') . ',email,' . $studentId,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|string|url',
            'gender' => 'nullable|in:male,female,other',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|in:active,inactive,suspended',
            'remark' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => '学生姓名不能为空',
            'name.max' => '学生姓名不能超过100个字符',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '该邮箱已被注册',
            'gender.in' => '性别不正确',
            'status.in' => '状态不正确',
        ];
    }
}
