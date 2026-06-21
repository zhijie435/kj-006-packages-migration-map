@extends('shearerline::layouts.app')

@section('title', '报名列表 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">报名管理</h1>
    <a href="{{ route('shearerline.enrollments.create') }}" class="btn btn-primary">新建报名</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('shearerline.enrollments.index') }}" class="row g-3">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="搜索学生/课程..." value="{{ $filters['keyword'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select">
                    <option value="">全部课程</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}" {{ ($filters['course_id'] ?? '') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="student_id" class="form-select">
                    <option value="">全部学生</option>
                    @foreach($students as $id => $name)
                        <option value="{{ $id }}" {{ ($filters['student_id'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">全部状态</option>
                    <option value="enrolled" {{ ($filters['status'] ?? '') == 'enrolled' ? 'selected' : '' }}>已报名</option>
                    <option value="completed" {{ ($filters['status'] ?? '') == 'completed' ? 'selected' : '' }}>已完成</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') == 'cancelled' ? 'selected' : '' }}>已取消</option>
                    <option value="refunded" {{ ($filters['status'] ?? '') == 'refunded' ? 'selected' : '' }}>已退款</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">搜索</button>
                <a href="{{ route('shearerline.enrollments.index') }}" class="btn btn-secondary">重置</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>课程</th>
                        <th>学生</th>
                        <th>报名时间</th>
                        <th>学习进度</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->id }}</td>
                            <td>{{ $enrollment->course->title ?? '-' }}</td>
                            <td>{{ $enrollment->student->name ?? '-' }}</td>
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
                            <td>{{ $enrollment->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('shearerline.enrollments.show', $enrollment) }}" class="btn btn-sm btn-info">查看</a>
                                <a href="{{ route('shearerline.enrollments.edit', $enrollment) }}" class="btn btn-sm btn-warning">编辑</a>
                                @if($enrollment->status === 'enrolled')
                                    <form method="POST" action="{{ route('shearerline.enrollments.cancel', $enrollment) }}" class="d-inline" onsubmit="return confirm('确定要取消报名吗?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary">取消</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('shearerline.enrollments.destroy', $enrollment) }}" class="d-inline" onsubmit="return confirm('确定要删除吗?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">删除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted">暂无数据</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $enrollments->links() }}
    </div>
</div>
@endsection
