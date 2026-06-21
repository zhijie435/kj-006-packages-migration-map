@extends('shearerline::layouts.app')

@section('title', '课程详情 - ' . ($course->title ?? ''))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $course->title }}</h1>
    <div>
        <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-secondary">课时管理</a>
        <a href="{{ route('shearerline.courses.edit', $course) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('shearerline.courses.index') }}" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">课程信息</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课程ID</div>
                    <div class="col-md-8">{{ $course->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">授课教师</div>
                    <div class="col-md-8">{{ $course->teacher->name ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课程简介</div>
                    <div class="col-md-8">{{ $course->description ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课程价格</div>
                    <div class="col-md-8">¥{{ number_format($course->price, 2) }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">总时长</div>
                    <div class="col-md-8">{{ $course->duration }} 分钟</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">难度等级</div>
                    <div class="col-md-8">{{ $course->difficulty }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课程分类</div>
                    <div class="col-md-8">{{ $course->category ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">状态</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                            {{ $course->status }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">开课日期</div>
                    <div class="col-md-8">{{ $course->start_date ? $course->start_date->format('Y-m-d') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">结课日期</div>
                    <div class="col-md-8">{{ $course->end_date ? $course->end_date->format('Y-m-d') : '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">创建时间</div>
                    <div class="col-md-8">{{ $course->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-muted">更新时间</div>
                    <div class="col-md-8">{{ $course->updated_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                课时列表
                <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-sm btn-primary">管理课时</a>
            </div>
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
                            @forelse($course->lessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->sort_order }}</td>
                                    <td>{{ $lesson->title }}</td>
                                    <td>{{ $lesson->duration }} 分钟</td>
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
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">暂无课时</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">课程统计</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">课时数量</div>
                    <div class="h4">{{ count($course->lessons) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">报名人数</div>
                    <div class="h4">{{ count($course->enrollments) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">已完成学习</div>
                    <div class="h4 text-success">{{ $course->enrollments->where('status', 'completed')->count() }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">总收入</div>
                    <div class="h4 text-primary">¥{{ number_format($course->enrollments->where('status', '!=', 'refunded')->sum('price'), 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">报名记录</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>学生</th>
                                <th>状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->enrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->student->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'completed' ? 'success' : ($enrollment->status === 'cancelled' || $enrollment->status === 'refunded' ? 'danger' : 'primary') }}">
                                            {{ $enrollment->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">暂无报名</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
