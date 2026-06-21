@extends('shearerline::layouts.app')

@section('title', '学生列表 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">学生管理</h1>
    <a href="{{ route('shearerline.students.create') }}" class="btn btn-primary">新建学生</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('shearerline.students.index') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="keyword" class="form-control" placeholder="搜索姓名/邮箱/电话..." value="{{ $filters['keyword'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <select name="gender" class="form-select">
                    <option value="">全部性别</option>
                    <option value="male" {{ ($filters['gender'] ?? '') == 'male' ? 'selected' : '' }}>男</option>
                    <option value="female" {{ ($filters['gender'] ?? '') == 'female' ? 'selected' : '' }}>女</option>
                    <option value="other" {{ ($filters['gender'] ?? '') == 'other' ? 'selected' : '' }}>其他</option>
                </select>
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
                <a href="{{ route('shearerline.students.index') }}" class="btn btn-secondary">重置</a>
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
                        <th>邮箱</th>
                        <th>电话</th>
                        <th>性别</th>
                        <th>年龄</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->name }}</td>
                            <td>{{ $student->email ?? '-' }}</td>
                            <td>{{ $student->phone ?? '-' }}</td>
                            <td>{{ $student->gender_text }}</td>
                            <td>{{ $student->age ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $student->status === 'active' ? 'success' : ($student->status === 'inactive' ? 'warning' : 'danger') }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td>{{ $student->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('shearerline.students.show', $student) }}" class="btn btn-sm btn-info">查看</a>
                                <a href="{{ route('shearerline.students.edit', $student) }}" class="btn btn-sm btn-warning">编辑</a>
                                <form method="POST" action="{{ route('shearerline.students.destroy', $student) }}" class="d-inline" onsubmit="return confirm('确定要删除吗?')">
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
        {{ $students->links() }}
    </div>
</div>
@endsection
