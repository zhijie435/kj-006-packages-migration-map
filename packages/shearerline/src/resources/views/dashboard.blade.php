@extends('shearerline::layouts.app')

@section('title', '控制台 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">控制台</h1>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-value text-primary">{{ $statistics['courses_count'] ?? 0 }}</div>
                <div class="stat-label">课程总数</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-value text-success">{{ $statistics['published_courses_count'] ?? 0 }}</div>
                <div class="stat-label">已发布课程</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-value text-info">{{ $statistics['students_count'] ?? 0 }}</div>
                <div class="stat-label">学生总数</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="stat-value text-warning">{{ $statistics['enrollments_count'] ?? 0 }}</div>
                <div class="stat-label">报名总数</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">最新课程</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>课程名称</th>
                                <th>状态</th>
                                <th>创建时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statistics['recent_courses'] ?? [] as $course)
                                <tr>
                                    <td>{{ $course->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                                            {{ $course->status }}
                                        </span>
                                    </td>
                                    <td>{{ $course->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">暂无数据</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">最新报名</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>学生</th>
                                <th>课程</th>
                                <th>状态</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($statistics['recent_enrollments'] ?? [] as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->student->name ?? '-' }}</td>
                                    <td>{{ $enrollment->course->title ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $enrollment->status === 'completed' ? 'success' : ($enrollment->status === 'cancelled' ? 'danger' : 'primary') }}">
                                            {{ $enrollment->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">暂无数据</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
