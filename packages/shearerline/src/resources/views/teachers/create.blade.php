@extends('shearerline::layouts.app')

@section('title', '新建教师 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">新建教师</h1>
    <a href="{{ route('shearerline.teachers.index') }}" class="btn btn-secondary">返回列表</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shearerline.teachers.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label">姓名 <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="title" class="form-label">职称</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="如: 教授、讲师、高级工程师等">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">邮箱</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label">电话</label>
                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="avatar" class="form-label">头像URL</label>
                <input type="text" name="avatar" id="avatar" class="form-control @error('avatar') is-invalid @enderror" value="{{ old('avatar') }}">
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="experience_years" class="form-label">教学经验 (年)</label>
                <input type="number" name="experience_years" id="experience_years" min="0" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', 0) }}">
                @error('experience_years')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="specialties" class="form-label">专业方向 (逗号分隔)</label>
                <input type="text" name="specialties" id="specialties" class="form-control @error('specialties') is-invalid @enderror" value="{{ old('specialties') }}" placeholder="如: 前端开发,后端开发,人工智能">
                @error('specialties')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="bio" class="form-label">个人简介</label>
                <textarea name="bio" id="bio" rows="4" class="form-control @error('bio') is-invalid @enderror">{{ old('bio') }}</textarea>
                @error('bio')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">状态</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>活跃</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>未激活</option>
                    <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>已停用</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="remark" class="form-label">备注</label>
                <textarea name="remark" id="remark" rows="3" class="form-control @error('remark') is-invalid @enderror">{{ old('remark') }}</textarea>
                @error('remark')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">创建教师</button>
                <a href="{{ route('shearerline.teachers.index') }}" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
