@extends('shearerline::layouts.app')

@section('title', '报名详情 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">报名详情 #{{ $enrollment->id }}</h1>
    <div>
        <a href="{{ route('shearerline.enrollments.edit', $enrollment) }}" class="btn btn-warning">编辑</a>
        @if($enrollment->status === 'enrolled')
            <form method="POST" action="{{ route('shearerline.enrollments.cancel', $enrollment) }}" class="d-inline" onsubmit="return confirm('确定要取消报名吗?')">
                @csrf
                <button type="submit" class="btn btn-secondary">取消报名</button>
            </form>
        @endif
        <a href="{{ route('shearerline.enrollments.index') }}" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">报名信息</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">报名ID</div>
                    <div class="col-md-8">{{ $enrollment->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课程</div>
                    <div class="col-md-8">
                        @if($enrollment->course)
                            <a href="{{ route('shearerline.courses.show', $enrollment->course) }}">
                                {{ $enrollment->course->title }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">学生</div>
                    <div class="col-md-8">
                        @if($enrollment->student)
                            <a href="{{ route('shearerline.students.show', $enrollment->student) }}">
                                {{ $enrollment->student->name }}
                            </a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">授课教师</div>
                    <div class="col-md-8">{{ $enrollment->course->teacher->name ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">报名时间</div>
                    <div class="col-md-8">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">学习进度</div>
                    <div class="col-md-8">
                        <div class="progress" style="height: 24px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $enrollment->progress ?? 0 }}%;">
                                {{ $enrollment->progress ?? 0 }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">状态</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $enrollment->status === 'completed' ? 'success' : ($enrollment->status === 'cancelled' || $enrollment->status === 'refunded' ? 'danger' : 'primary') }}">
                            {{ $enrollment->status_text }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">完成时间</div>
                    <div class="col-md-8">{{ $enrollment->completed_at ? $enrollment->completed_at->format('Y-m-d H:i:s') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">备注</div>
                    <div class="col-md-8">{{ $enrollment->remark ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">创建时间</div>
                    <div class="col-md-8">{{ $enrollment->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-muted">更新时间</div>
                    <div class="col-md-8">{{ $enrollment->updated_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>

        @if($enrollment->course && count($enrollment->course->lessons) > 0)
            <div class="card">
                <div class="card-header">课程课时</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>序号</th>
                                    <th>课时名称</th>
                                    <th>时长</th>
                                    <th>免费</th>
                                    <th>状态</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollment->course->lessons as $lesson)
                                    <tr>
                                        <td>{{ $lesson->sort_order }}</td>
                                        <td>{{ $lesson->title }}</td>
                                        <td>{{ $lesson->formatted_duration }}</td>
                                        <td>
                                            @if($lesson->is_free)
                                                <span class="badge bg-success">免费</span>
                                            @else
                                                <span class="badge bg-secondary">付费</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $lesson->status === 'published' ? 'success' : 'warning' }}">
                                                {{ $lesson->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">课程信息</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">课程价格</div>
                    <div class="h4 text-primary">¥{{ number_format($enrollment->course->price ?? 0, 2) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">课程难度</div>
                    <div class="h5">{{ $enrollment->course->difficulty ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">课时总数</div>
                    <div class="h4">{{ count($enrollment->course->lessons ?? []) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">总时长</div>
                    <div class="h5">{{ ($enrollment->course->total_lessons_duration ?? 0) }} 分钟</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">学生信息</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">邮箱</div>
                    <div>{{ $enrollment->student->email ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">电话</div>
                    <div>{{ $enrollment->student->phone ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">报名课程数</div>
                    <div class="h4">{{ $enrollment->student->enrolled_courses_count ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
