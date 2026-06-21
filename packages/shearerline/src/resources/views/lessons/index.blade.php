@extends('shearerline::layouts.app')

@section('title', '课时列表 - ' . ($course->title ?? ''))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.index') }}">课程管理</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.show', $course) }}">{{ $course->title }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">课时管理</li>
            </ol>
        </nav>
        <h1 class="h2">课时管理 - {{ $course->title }}</h1>
    </div>
    <div>
        <a href="{{ route('shearerline.courses.show', $course) }}" class="btn btn-outline-secondary">返回课程</a>
        <a href="{{ route('shearerline.courses.lessons.create', $course) }}" class="btn btn-primary">新建课时</a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('shearerline.courses.lessons.index', $course) }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="搜索课时名称..." value="{{ $filters['keyword'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="is_free" class="form-select">
                    <option value="">全部</option>
                    <option value="1" {{ ($filters['is_free'] ?? '') == '1' ? 'selected' : '' }}>免费</option>
                    <option value="0" {{ ($filters['is_free'] ?? '') == '0' ? 'selected' : '' }}>付费</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">全部状态</option>
                    <option value="draft" {{ ($filters['status'] ?? '') == 'draft' ? 'selected' : '' }}>草稿</option>
                    <option value="published" {{ ($filters['status'] ?? '') == 'published' ? 'selected' : '' }}>已发布</option>
                    <option value="archived" {{ ($filters['status'] ?? '') == 'archived' ? 'selected' : '' }}>已归档</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">搜索</button>
                <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-secondary">重置</a>
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
                        <th>序号</th>
                        <th>课时名称</th>
                        <th>时长</th>
                        <th>免费</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lessons as $lesson)
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
                                <span class="badge bg-{{ $lesson->status === 'published' ? 'success' : ($lesson->status === 'draft' ? 'warning' : 'secondary') }}">
                                    {{ $lesson->status }}
                                </span>
                            </td>
                            <td>{{ $lesson->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('shearerline.courses.lessons.show', [$course, $lesson]) }}" class="btn btn-sm btn-info">查看</a>
                                <a href="{{ route('shearerline.courses.lessons.edit', [$course, $lesson]) }}" class="btn btn-sm btn-warning">编辑</a>
                                <form method="POST" action="{{ route('shearerline.courses.lessons.destroy', [$course, $lesson]) }}" class="d-inline" onsubmit="return confirm('确定要删除吗?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">删除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">暂无数据</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $lessons->links() }}
    </div>
</div>
@endsection
