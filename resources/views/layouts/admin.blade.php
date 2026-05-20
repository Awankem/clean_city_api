<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — CleanCity Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Public+Sans:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    <link href="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    @yield('scripts_head')

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="admin-shell text-on-surface font-body min-h-screen" x-data="{ mobileMenu: false }">

    <!-- Desktop sidebar -->
    <aside class="hidden lg:flex flex-col fixed left-0 top-0 h-screen w-[17.5rem] bg-surface-container-lowest z-40 border-r border-outline-variant/15 shadow-sm">
        @include('layouts.partials.admin-sidebar-content')
    </aside>

    <!-- Mobile sidebar -->
    <div class="lg:hidden fixed inset-0 z-50" x-show="mobileMenu" x-cloak>
        <div class="absolute inset-0 bg-on-surface/40 backdrop-blur-sm" x-on:click="mobileMenu = false"></div>
        <aside class="absolute left-0 top-0 h-full w-72 bg-surface-container-lowest flex flex-col shadow-2xl"
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">
            @include('layouts.partials.admin-sidebar-content')
        </aside>
    </div>

    <!-- Top bar -->
    <header class="fixed top-0 right-0 left-0 lg:left-[17.5rem] h-16 z-30 bg-surface-container-lowest/90 backdrop-blur-lg border-b border-outline-variant/15 flex items-center justify-between gap-4 px-4 md:px-8">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <button type="button" class="lg:hidden p-2 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors"
                    x-on:click="mobileMenu = true" aria-label="Open menu">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="min-w-0 hidden sm:block">
                <p class="text-[10px] font-bold text-primary uppercase tracking-widest">@yield('breadcrumb', 'Admin')</p>
                <p class="text-sm font-bold text-on-surface truncate">@yield('title', 'Dashboard')</p>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-3 shrink-0">
            <div class="hidden md:flex items-center gap-2 bg-surface-container rounded-xl px-3 py-2 border border-outline-variant/10 w-56 lg:w-64">
                <span class="material-symbols-outlined text-on-surface-variant text-lg">search</span>
                <input type="search" class="bg-transparent border-0 text-sm text-on-surface w-full focus:ring-0 p-0 placeholder:text-on-surface-variant/70"
                       placeholder="Search reports…" aria-label="Search reports">
            </div>

            <button type="button" class="w-10 h-10 flex items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors" title="Notifications">
                <span class="material-symbols-outlined">notifications</span>
            </button>

            <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-outline-variant/15">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=00482f&color=fff&size=80"
                     alt="" class="w-9 h-9 rounded-full ring-2 ring-primary/20">
                <div class="hidden lg:block text-left">
                    <p class="text-xs font-bold leading-tight truncate max-w-[120px]">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-on-surface-variant capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-10 h-10 flex items-center justify-center rounded-xl text-tertiary hover:bg-tertiary/10 transition-colors" title="Sign out">
                    <span class="material-symbols-outlined">logout</span>
                </button>
            </form>
        </div>
    </header>

    <main class="lg:ml-[17.5rem] pt-16 min-h-screen">
        <div class="p-4 md:p-8 max-w-[1600px]">
            @if(session('success'))
                <div class="admin-alert-success mb-6" role="alert">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
