@extends('shearerline::layouts.app')

@section('title', '新建报名 - Shearerline')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">新建报名</h1>
    <a href="{{ route('shearerline.enrollments.index') }}" class="btn btn-secondary">返回列表</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shearerline.enrollments.store') }}" class="row g-3">
            @csrf

            <div class="col-md-6">
                <label for="course_id" class="form-label">课程 <span class="text-danger">*</span></label>
                <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                    <option value="">请选择课程</option>
                    @foreach($courses as $id => $title)
                        <option value="{{ $id }}" {{ old('course_id') == $id ? 'selected' : '' }}>{{ $title }}</option>
                    @endforeach
                </select>
                @error('course_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="student_id" class="form-label">学生 <span class="text-danger">*</span></label>
                <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                    <option value="">请选择学生</option>
                    @foreach($students as $id => $name)
                        <option value="{{ $id }}" {{ old('student_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('student_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="enrolled_at" class="form-label">报名时间</label>
                <input type="datetime-local" name="enrolled_at" id="enrolled_at" class="form-control @error('enrolled_at') is-invalid @enderror" value="{{ old('enrolled_at') }}">
                @error('enrolled_at')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="progress" class="form-label">学习进度 (%)</label>
                <input type="number" name="progress" id="progress" min="0" max="100" step="0.01" class="form-control @error('progress') is-invalid @enderror" value="{{ old('progress', 0) }}">
                @error('progress')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="status" class="form-label">状态</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="enrolled" {{ old('status') == 'enrolled' ? 'selected' : '' }}>已报名</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>已完成</option>
                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>已取消</option>
                    <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>已退款</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="completed_at" class="form-label">完成时间</label>
                <input type="datetime-local" name="completed_at" id="completed_at" class="form-control @error('completed_at') is-invalid @enderror" value="{{ old('completed_at') }}">
                @error('completed_at')
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
                <button type="submit" class="btn btn-primary">创建报名</button>
                <a href="{{ route('shearerline.enrollments.index') }}" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
