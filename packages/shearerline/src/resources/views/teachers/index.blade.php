@extends('shearerline::layouts.app')

@section('title', '教师列表 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">教师管理</h1>
    <a href="{{ route('shearerline.teachers.create') }}" class="btn btn-primary">新建教师</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('shearerline.teachers.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="搜索姓名/邮箱/电话..." value="{{ $filters['keyword'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="specialty" class="form-control" placeholder="搜索专业方向..." value="{{ $filters['specialty'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">全部状态</option>
                    <option value="active" {{ ($filters['status'] ?? '') == 'active' ? 'selected' : '' }}>活跃</option>
                    <option value="inactive" {{ ($filters['status'] ?? '') == 'inactive' ? 'selected' : '' }}>未激活</option>
                    <option value="suspended" {{ ($filters['status'] ?? '') == 'suspended' ? 'selected' : '' }}>已停用</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">搜索</button>
                <a href="{{ route('shearerline.teachers.index') }}" class="btn btn-secondary">重置</a>
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
                        <th>姓名</th>
                        <th>职称</th>
                        <th>邮箱</th>
                        <th>电话</th>
                        <th>教龄</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td>{{ $teacher->id }}</td>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ $teacher->title ?? '-' }}</td>
                            <td>{{ $teacher->email ?? '-' }}</td>
                            <td>{{ $teacher->phone ?? '-' }}</td>
                            <td>{{ $teacher->experience_years ?? 0 }} 年</td>
                            <td>
                                <span class="badge bg-{{ $teacher->status === 'active' ? 'success' : ($teacher->status === 'inactive' ? 'warning' : 'danger') }}">
                                    {{ $teacher->status }}
                                </span>
                            </td>
                            <td>{{ $teacher->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('shearerline.teachers.show', $teacher) }}" class="btn btn-sm btn-info">查看</a>
                                <a href="{{ route('shearerline.teachers.edit', $teacher) }}" class="btn btn-sm btn-warning">编辑</a>
                                <form method="POST" action="{{ route('shearerline.teachers.destroy', $teacher) }}" class="d-inline" onsubmit="return confirm('确定要删除吗?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">删除</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted">暂无数据</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $teachers->links() }}
    </div>
</div>
@endsection
