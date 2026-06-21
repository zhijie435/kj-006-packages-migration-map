@extends('shearerline::layouts.app')

@section('title', '新建学生 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">新建学生</h1>
    <a href="{{ route('shearerline.students.index') }}" class="btn btn-secondary">返回列表</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shearerline.students.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="name" class="form-label">姓名 <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="gender" class="form-label">性别</label>
                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                    <option value="">请选择</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>男</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>女</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>其他</option>
                </select>
                @error('gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="birth_date" class="form-label">出生日期</label>
                <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}">
                @error('birth_date')
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

            <div class="col-md-12">
                <label for="address" class="form-label">地址</label>
                <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                @error('address')
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
                <button type="submit" class="btn btn-primary">创建学生</button>
                <a href="{{ route('shearerline.students.index') }}" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
