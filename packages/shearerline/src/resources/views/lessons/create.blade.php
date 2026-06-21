@extends('shearerline::layouts.app')

@section('title', '新建课时 - ' . ($course->title ?? ''))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.index') }}">课程管理</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.show', $course) }}">{{ $course->title }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.lessons.index', $course) }}">课时管理</a></li>
                <li class="breadcrumb-item active" aria-current="page">新建课时</li>
            </ol>
        </nav>
        <h1 class="h2">新建课时 - {{ $course->title }}</h1>
    </div>
    <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-secondary">返回列表</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('shearerline.courses.lessons.store', $course) }}" class="row g-3">
            @csrf

            <div class="col-md-8">
                <label for="title" class="form-label">课时名称 <span class="text-danger">*</span></label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label for="sort_order" class="form-label">排序</label>
                <input type="number" name="sort_order" id="sort_order" min="0" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-2">
                <label for="duration" class="form-label">时长 (分钟)</label>
                <input type="number" name="duration" id="duration" min="0" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration', 0) }}">
                @error('duration')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="description" class="form-label">课时简介</label>
                <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="video_url" class="form-label">视频链接</label>
                <input type="text" name="video_url" id="video_url" class="form-control @error('video_url') is-invalid @enderror" value="{{ old('video_url') }}" placeholder="https://...">
                @error('video_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12">
                <label for="content" class="form-label">课时内容</label>
                <textarea name="content" id="content" rows="8" class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
                <label for="is_free" class="form-label">是否免费</label>
                <select name="is_free" id="is_free" class="form-select @error('is_free') is-invalid @enderror">
                    <option value="0" {{ old('is_free') == '0' ? 'selected' : '' }}>付费</option>
                    <option value="1" {{ old('is_free') == '1' ? 'selected' : '' }}>免费</option>
                </select>
                @error('is_free')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4">
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

            <div class="col-12">
                <hr>
                <button type="submit" class="btn btn-primary">创建课时</button>
                <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>
</div>
@endsection
