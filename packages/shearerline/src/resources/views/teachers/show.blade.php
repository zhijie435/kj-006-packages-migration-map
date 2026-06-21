@extends('shearerline::layouts.app')

@section('title', '教师详情 - ' . ($teacher->name ?? ''))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $teacher->name }}</h1>
    <div>
        <a href="{{ route('shearerline.teachers.edit', $teacher) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('shearerline.teachers.index') }}" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">基本信息</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">教师ID</div>
                    <div class="col-md-8">{{ $teacher->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">姓名</div>
                    <div class="col-md-8">{{ $teacher->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">职称</div>
                    <div class="col-md-8">{{ $teacher->title ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">邮箱</div>
                    <div class="col-md-8">{{ $teacher->email ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">电话</div>
                    <div class="col-md-8">{{ $teacher->phone ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">教学经验</div>
                    <div class="col-md-8">{{ $teacher->experience_years ?? 0 }} 年</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">专业方向</div>
                    <div class="col-md-8">
                        @if($teacher->specialties)
                            @foreach(is_array($teacher->specialties) ? $teacher->specialties : explode(',', $teacher->specialties) as $specialty)
                                <span class="badge bg-secondary me-1">{{ trim($specialty) }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">个人简介</div>
                    <div class="col-md-8">{{ $teacher->bio ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">状态</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : ($teacher->status === 'inactive' ? 'warning' : 'danger') }}">
                            {{ $teacher->status }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">备注</div>
                    <div class="col-md-8">{{ $teacher->remark ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">创建时间</div>
                    <div class="col-md-8">{{ $teacher->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-muted">更新时间</div>
                    <div class="col-md-8">{{ $teacher->updated_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">教授课程</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>课程名称</th>
                                <th>价格</th>
                                <th>难度</th>
                                <th>报名人数</th>
                                <th>状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teacher->courses as $course)
                                <tr>
                                    <td>
                                        <a href="{{ route('shearerline.courses.show', $course) }}">
                                            {{ $course->title }}
                                        </a>
                                    </td>
                                    <td>¥{{ number_format($course->price, 2) }}</td>
                                    <td>{{ $course->difficulty }}</td>
                                    <td>{{ count($course->enrollments) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                                            {{ $course->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">暂无课程</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">教学统计</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">课程总数</div>
                    <div class="h4">{{ count($teacher->courses) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">已发布课程</div>
                    <div class="h4 text-success">{{ $teacher->published_courses_count ?? 0 }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">学生总数</div>
                    <div class="h4 text-primary">{{ $teacher->total_students_count ?? 0 }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">总收入</div>
                    <div class="h4 text-warning">
                        ¥{{ number_format($teacher->courses->sum(function($c) { return $c->enrollments->where('status', '!=', 'refunded')->sum('price'); }), 2) }}
                    </div>
                </div>
            </div>
        </div>

        @if($teacher->avatar)
            <div class="card">
                <div class="card-header">头像</div>
                <div class="card-body text-center">
                    <img src="{{ $teacher->avatar }}" alt="{{ $teacher->name }}" class="img-fluid rounded">
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
