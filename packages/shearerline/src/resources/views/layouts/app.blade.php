<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Shearerline - 在线课程教务系统')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #343a40; }
        .sidebar .nav-link { color: #fff; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #495057; }
        .main-content { padding: 2rem; }
        .stat-card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        .stat-card .card-body { padding: 1.5rem; }
        .stat-card .stat-value { font-size: 2rem; font-weight: bold; }
        .stat-card .stat-label { color: #6c757d; font-size: 0.9rem; }
    </style>
    @stack('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar">
            <div class="position-sticky pt-3">
                <h5 class="text-white px-3 mb-3">Shearerline</h5>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shearerline.dashboard') ? 'active' : '' }}" href="{{ route('shearerline.dashboard') }}">
                            控制台
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shearerline.courses.*') ? 'active' : '' }}" href="{{ route('shearerline.courses.index') }}">
                            课程管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shearerline.students.*') ? 'active' : '' }}" href="{{ route('shearerline.students.index') }}">
                            学生管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shearerline.teachers.*') ? 'active' : '' }}" href="{{ route('shearerline.teachers.index') }}">
                            教师管理
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shearerline.enrollments.*') ? 'active' : '' }}" href="{{ route('shearerline.enrollments.index') }}">
                            报名管理
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <main class="col-md-10 ms-sm-auto main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
