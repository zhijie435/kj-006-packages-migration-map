@extends('shearerline::layouts.app')

@section('title', '新建课程 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">新建课程</h1>
    <a href="{{ route('shearerline.courses.index') }}" class="btn btn-secondary">返回列表</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shearerline.courses.store') }}" class="row g-3">
            @csrf

            <div class="col-md-8">
                <label for="title" class="form-label">课程名称 <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="teacher_id" class="form-label">授课教师 <span class="text-danger">*</span></label>
                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                    <option value="">请选择教师</option>
                    @foreach($teachers as $id => $name)
                        <option value="{{ $id }}" {{ old('teacher_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="description" class="form-label">课程简介</label>
                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="price" class="form-label">课程价格 (元)</label>
                <input type="number" name="price" id="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', 0) }}">
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="duration" class="form-label">总时长 (分钟)</label>
                <input type="number" name="duration" id="duration" min="0" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration', 0) }}">
                @error('duration')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="difficulty" class="form-label">难度等级</label>
                <select name="difficulty" id="difficulty" class="form-select @error('difficulty') is-invalid @enderror">
                    <option value="beginner" {{ old('difficulty') == 'beginner' ? 'selected' : '' }}>入门</option>
                    <option value="intermediate" {{ old('difficulty') == 'intermediate' ? 'selected' : '' }}>中级</option>
                    <option value="advanced" {{ old('difficulty') == 'advanced' ? 'selected' : '' }}>高级</option>
                </select>
                @error('difficulty')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="category" class="form-label">课程分类</label>
                <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}">
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label">状态</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>草稿</option>
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>已发布</option>
                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>已归档</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="start_date" class="form-label">开课日期</label>
                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                @error('start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-5">
                <label for="end_date" class="form-label">结课日期</label>
                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                @error('end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">创建课程</button>
                <a href="{{ route('shearerline.courses.index') }}" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
