@php
    $nav = [
        ['route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'icon' => 'assignment', 'label' => 'Reports'],
        ['route' => 'admin.analytics', 'match' => 'admin.analytics', 'icon' => 'leaderboard', 'label' => 'Analytics'],
        ['route' => 'admin.hotspots', 'match' => 'admin.hotspots', 'icon' => 'distance', 'label' => 'Hotspots'],
        ['route' => 'admin.audit', 'match' => 'admin.audit', 'icon' => 'history', 'label' => 'Audit Logs'],
    ];
@endphp

<div class="flex flex-col h-full py-5">
    <div class="px-5 mb-6 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-surface-container-low flex items-center justify-center border border-outline-variant/15 overflow-hidden p-1.5 shrink-0">
            <img src="{{ asset('img/logo.png') }}" alt="CleanCity" class="w-full h-full object-contain">
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-black text-primary font-heading tracking-tight leading-none truncate">CleanCity</h1>
            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider mt-0.5">Admin Portal</p>
        </div>
    </div>

    <p class="px-5 mb-2 text-[10px] font-bold text-on-surface-variant/70 uppercase tracking-widest">Menu</p>
    <nav class="flex-1 px-2 space-y-1 overflow-y-auto custom-scrollbar font-heading">
        @foreach($nav as $item)
            <a href="{{ route($item['route']) }}"
               class="admin-nav-link {{ request()->routeIs($item['match']) ? 'admin-nav-link--active' : 'admin-nav-link--inactive' }}">
                <span class="material-symbols-outlined text-[22px]">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="px-5 pt-4 mt-4 border-t border-outline-variant/15">
        <div class="flex items-center gap-3 p-3 rounded-xl bg-surface-container-low">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=00482f&color=fff&size=72"
                 alt="" class="w-10 h-10 rounded-full shrink-0">
            <div class="min-w-0">
                <p class="text-sm font-bold truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-on-surface-variant uppercase">{{ Auth::user()->role }} account</p>
            </div>
        </div>
    </div>
</div>
