<?php

namespace Shearerline\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'sometimes|required|in:enrolled,completed,cancelled,refunded',
            'progress' => 'nullable|numeric|min:0|max:100',
            'completed_at' => 'nullable|date',
            'remark' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => '状态不能为空',
            'status.in' => '状态不正确',
            'progress.numeric' => '进度必须是数字',
            'progress.min' => '进度不能小于0',
            'progress.max' => '进度不能超过100',
        ];
    }
}
