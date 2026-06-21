@extends('shearerline::layouts.app')

@section('title', '课时详情 - ' . ($lesson->title ?? ''))

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.index') }}">课程管理</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.show', $course) }}">{{ $course->title }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shearerline.courses.lessons.index', $course) }}">课时管理</a></li>
                <li class="breadcrumb-item active" aria-current="page">课时详情</li>
            </ol>
        </nav>
        <h1 class="h2">{{ $lesson->title }}</h1>
    </div>
    <div>
        <a href="{{ route('shearerline.courses.lessons.edit', [$course, $lesson]) }}" class="btn btn-warning">编辑</a>
        <a href="{{ route('shearerline.courses.lessons.index', $course) }}" class="btn btn-outline-secondary">返回列表</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">课时信息</div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课时ID</div>
                    <div class="col-md-8">{{ $lesson->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">所属课程</div>
                    <div class="col-md-8">
                        <a href="{{ route('shearerline.courses.show', $course) }}">
                            {{ $course->title }}
                        </a>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课时名称</div>
                    <div class="col-md-8">{{ $lesson->title }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">序号</div>
                    <div class="col-md-8">{{ $lesson->sort_order }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">时长</div>
                    <div class="col-md-8">{{ $lesson->formatted_duration }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">是否免费</div>
                    <div class="col-md-8">
                        @if($lesson->is_free)
                            <span class="badge bg-success">免费</span>
                        @else
                            <span class="badge bg-secondary">付费</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">状态</div>
                    <div class="col-md-8">
                        <span class="badge bg-{{ $lesson->status === 'published' ? 'success' : ($lesson->status === 'draft' ? 'warning' : 'secondary') }}">
                            {{ $lesson->status }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">视频链接</div>
                    <div class="col-md-8">
                        @if($lesson->video_url)
                            <a href="{{ $lesson->video_url }}" target="_blank">{{ $lesson->video_url }}</a>
                        @else
                            -
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">课时简介</div>
                    <div class="col-md-8">{{ $lesson->description ?? '-' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 text-muted">创建时间</div>
                    <div class="col-md-8">{{ $lesson->created_at->format('Y-m-d H:i:s') }}</div>
                </div>
                <div class="row">
                    <div class="col-md-4 text-muted">更新时间</div>
                    <div class="col-md-8">{{ $lesson->updated_at->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">课时内容</div>
            <div class="card-body">
                @if($lesson->content)
                    <div class="lesson-content">
                        {!! nl2br(e($lesson->content)) !!}
                    </div>
                @else
                    <p class="text-muted">暂无内容</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">课程概览</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small">课程名称</div>
                    <div class="h6">{{ $course->title }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">授课教师</div>
                    <div>{{ $course->teacher->name ?? '-' }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">总课时数</div>
                    <div class="h4">{{ count($course->lessons) }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted small">当前课时</div>
                    <div class="h5 text-primary">第 {{ $lesson->sort_order }} 节</div>
                </div>
            </div>
        </div>

        @if($lesson->video_url)
            <div class="card mb-4">
                <div class="card-header">视频预览</div>
                <div class="card-body">
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $lesson->video_url }}" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header">同课程其他课时</div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($course->lessons->where('id', '!=', $lesson->id)->take(10) as $otherLesson)
                        <a href="{{ route('shearerline.courses.lessons.show', [$course, $otherLesson]) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $otherLesson->sort_order }}. {{ $otherLesson->title }}</h6>
                                <small>
                                    @if($otherLesson->is_free)
                                        <span class="badge bg-success">免费</span>
                                    @endif
                                </small>
                            </div>
                            <small class="text-muted">{{ $otherLesson->formatted_duration }}</small>
                        </a>
                    @endforeach
                    @if($course->lessons->where('id', '!=', $lesson->id)->count() == 0)
                        <p class="text-muted text-center mb-0">暂无其他课时</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
