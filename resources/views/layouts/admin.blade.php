<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCity Admin - @yield('title')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8f9fa; }
        .sidebar { height: 100vh; background: #1a1a1a; color: white; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #ccc; margin: 5px 15px; border-radius: 8px; transition: 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #10b981; color: white; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #10b981; border: none; }
        .btn-primary:hover { background-color: #059669; }
        .status-badge { border-radius: 20px; padding: 4px 12px; font-size: 0.85rem; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-in_progress { background: #dcfce7; color: #166534; }
        .status-resolved { background: #dbeafe; color: #1e40af; }
    </style>
    @yield('styles')
</head>
<body>

    <div class="sidebar d-flex flex-column p-3">
        <h3 class="text-center mb-4 fw-bold text-success">CleanCity</h3>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    All Reports
                </a>
            </li>
        </ul>
        <hr>
        <div class="px-3 pb-3">
            <span class="d-block small text-muted">Account</span>
            <strong>Admin</strong>
        </div>
    </div>

    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
