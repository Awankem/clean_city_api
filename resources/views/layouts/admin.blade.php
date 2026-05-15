<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCity Admin - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Public+Sans:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
    
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <!-- Mapbox GL JS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.1.2/mapbox-gl.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
    </style>
    @yield('styles')
    @yield('scripts_head')
    
    <!-- Alpine.js for simple UI interactions -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-surface text-on-surface font-body" x-data="{ mobileMenu: false }">
    
    <!-- Sidebar (Desktop) -->
    <aside class="hidden lg:flex flex-col fixed left-0 top-0 h-screen w-64 bg-surface-container-lowest z-40 py-6 border-r border-outline-variant/20 shadow-sm">
        @include('layouts.partials.admin-sidebar-content')
    </aside>

    <!-- Sidebar (Mobile Overlay) -->
    <div class="lg:hidden fixed inset-0 z-50 overflow-hidden" x-show="mobileMenu" x-cloak>
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" x-on:click="mobileMenu = false"></div>
        <aside class="absolute left-0 top-0 h-full w-72 bg-surface-container-lowest flex flex-col py-6"
               x-transition:enter="transition ease-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in duration-200 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full">
            @include('layouts.partials.admin-sidebar-content')
        </aside>
    </div>

    <!-- TopBar -->
    <header class="fixed top-0 right-0 left-0 lg:left-64 h-16 z-30 bg-surface-container-lowest/80 backdrop-blur-md shadow-sm border-b border-outline-variant/10 flex justify-between items-center px-4 md:px-8">
        <div class="flex items-center gap-4 flex-1">
            <button class="lg:hidden p-2 text-on-surface-variant hover:bg-surface-container rounded-lg" x-on:click="mobileMenu = true">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <div class="hidden sm:flex items-center bg-surface-container px-4 py-2 rounded-lg w-full max-w-md border border-outline-variant/5">
                <span class="material-symbols-outlined text-on-surface-variant text-sm mr-2" data-icon="search">search</span>
                <input type="text" class="bg-transparent border-none text-on-surface text-sm focus:ring-0 w-full placeholder:text-on-surface-variant" placeholder="Search reports...">
            </div>
        </div>
        <div class="flex items-center gap-2 md:gap-4">
            <button class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined" data-icon="notifications">notifications</span>
            </button>
            
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center rounded-full text-tertiary hover:bg-tertiary/10 transition-colors" title="Sign Out">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                </button>
            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-16 min-h-screen">
        <div class="p-4 md:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-primary/10 border border-primary/20 text-primary rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
                    <span class="material-symbols-outlined" data-icon="check_circle">check_circle</span>
                    <p class="text-sm font-bold">{{ session('success') }}</p>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
