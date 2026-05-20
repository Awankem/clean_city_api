<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCity — @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Public+Sans:ital,wght@0,700;0,900;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('head')
</head>
<body class="min-h-screen font-body admin-shell flex">
    <!-- Brand panel (desktop) -->
    <aside class="hidden lg:flex lg:w-[42%] xl:w-[45%] flex-col justify-between p-12 bg-primary text-on-primary relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none"
             style="background-image: radial-gradient(circle at 20% 80%, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative z-10">
            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center border border-white/20 p-2 mb-8">
                <img src="{{ asset('img/logo.png') }}" alt="CleanCity" class="w-full h-full object-contain">
            </div>
            <h1 class="text-4xl font-black font-heading tracking-tight leading-tight">CleanCity</h1>
            <p class="text-on-primary/80 mt-3 text-lg max-w-md leading-relaxed">Digital waste management for municipal teams — track reports, hotspots, and resolution in one place.</p>
        </div>
        <ul class="relative z-10 space-y-4 text-sm text-on-primary/70">
            <li class="flex items-center gap-3"><span class="material-symbols-outlined text-lg">map</span> Live hotspot mapping</li>
            <li class="flex items-center gap-3"><span class="material-symbols-outlined text-lg">analytics</span> Performance analytics</li>
            <li class="flex items-center gap-3"><span class="material-symbols-outlined text-lg">history</span> Full audit trail</li>
        </ul>
    </aside>

    <!-- Form panel -->
    <main class="flex-1 flex items-center justify-center p-6 md:p-10">
        <div class="w-full @hasSection('auth_width')@yield('auth_width')@else max-w-md @endif">
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 bg-surface-container-lowest rounded-2xl mx-auto flex items-center justify-center shadow-md border border-outline-variant/15 p-2 mb-4">
                    <img src="{{ asset('img/logo.png') }}" alt="CleanCity" class="w-full h-full object-contain">
                </div>
                <h1 class="text-2xl font-black text-primary font-heading">CleanCity</h1>
            </div>

            @yield('content')

            <p class="text-center mt-8 text-xs text-on-surface-variant">
                @yield('footer', 'Authorized personnel only. © ' . date('Y') . ' Municipal Waste Authority')
            </p>
        </div>
    </main>
</body>
</html>
