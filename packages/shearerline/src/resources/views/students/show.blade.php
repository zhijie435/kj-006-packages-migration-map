@extends('shearerline::layouts.app')

@section('title', '学生详情 - ' . ($student->name ?? ''))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $student->name }}</h1>
    <div>
        <a href="{{ route('shearerline.students.edit', $student) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('shearerline.students.index') }}" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">基本信息</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">学生ID</div>
                    <div class="col-md-8">{{ $student->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">姓名</div>
                    <div class="col-md-8">{{ $student->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">性别</div>
                    <div class="col-md-8">{{ $student->gender_text }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">年龄</div>
                    <div class="col-md-8">{{ $student->age ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">出生日期</div>
                    <div class="col-md-8">{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">邮箱</div>
                    <div class="col-md-8">{{ $student->email ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">电话</div>
                    <div class="col-md-8">{{ $student->phone ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">地址</div>
                    <div class="col-md-8">{{ $student->address ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">状态</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'inactive' ? 'warning' : 'danger') }}">
                            {{ $student->status }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">备注</div>
                    <div class="col-md-8">{{ $student->remark ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">创建时间</div>
                    <div class="col-md-8">{{ $student->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-muted">更新时间</div>
                    <div class="col-md-8">{{ $student->updated_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">报名课程</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>课程名称</th>
                                <th>授课教师</th>
                                <th>报名时间</th>
                                <th>进度</th>
                                <th>状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($student->enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <a href="{{ route('shearerline.courses.show', $enrollment->course) }}">
                                            {{ $enrollment->course->title ?? '-' }}
                                        </a>
                                    </td>
                                    <td>{{ $enrollment->course->teacher->name ?? '-' }}</td>
                                    <td>{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress ?? 0 }}%;">
                                                {{ $enrollment->progress ?? 0 }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'completed' ? 'success' : ($enrollment->status === 'cancelled' || $enrollment->status === 'refunded' ? 'danger' : 'primary') }}">
                                            {{ $enrollment->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">暂无报名记录</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">学生统计</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">报名课程数</div>
                    <div class="h4">{{ $student->enrolled_courses_count ?? 0 }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">已完成课程数</div>
                    <div class="h4 text-success">{{ $student->completed_courses_count ?? 0 }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">进行中课程</div>
                    <div class="h4 text-primary">
                        {{ ($student->enrollments->where('status', 'enrolled')->count()) ?? 0 }}
                    </div>
                </div>
            </div>
        </div>

        @if($student->avatar)
            <div class="card">
                <div class="card-header">头像</div>
                <div class="card-body text-center">
                    <img src="{{ $student->avatar }}" alt="{{ $student->name }}" class="img-fluid rounded">
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
