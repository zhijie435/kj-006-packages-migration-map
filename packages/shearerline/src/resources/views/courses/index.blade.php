@extends('shearerline::layouts.app')

@section('title', '课程列表 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">课程管理</h1>
    <a href="{{ route('shearerline.courses.create') }}" class="btn btn-primary">新建课程</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('shearerline.courses.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="搜索课程名称..." value="{{ $filters['keyword'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="teacher_id" class="form-select">
                    <option value="">全部教师</option>
                    @foreach($teachers as $id => $name)
                        <option value="{{ $id }}" {{ ($filters['teacher_id'] ?? '') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">全部状态</option>
                    <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>草稿</option>
                    <option value="published" {{ ($filters['status'] ?? '') == 'published' ? 'selected' : '' }}>已发布</option>
                    <option value="archived" {{ ($filters['status'] ?? '') == 'archived' ? 'selected' : '' }}>已归档</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">搜索</button>
                <a href="{{ route('shearerline.courses.index') }}" class="btn btn-secondary">重置</a>
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
                        <th>课程名称</th>
                        <th>教师</th>
                        <th>价格</th>
                        <th>难度</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->id }}</td>
                            <td>{{ $course->title }}</td>
                            <td>{{ $course->teacher->name ?? '-' }}</td>
                            <td>¥{{ number_format($course->price, 2) }}</td>
                            <td>{{ $course->difficulty }}</td>
                            <td>
                                <span class="badge bg-{{ $course->status === 'published' ? 'success' : ($course->status === 'draft' ? 'warning' : 'secondary') }}">
                                    {{ $course->status }}
                                </span>
                            </td>
                            <td>{{ $course->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('shearerline.courses.show', $course) }}" class="btn btn-sm btn-info">查看</a>
                                <a href="{{ route('shearerline.courses.edit', $course) }}" class="btn btn-sm btn-warning">编辑</a>
                                <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-sm btn-secondary">课时</a>
                                <form method="POST" action="{{ route('shearerline.courses.destroy', $course) }}" class="d-inline" onsubmit="return confirm('确定要删除吗?')">
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
        {{ $courses->links() }}
    </div>
</div>
@endsection
