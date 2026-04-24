<div class="px-6 mb-8 flex items-center gap-3">
    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center border border-primary/20">
        <span class="material-symbols-outlined text-primary" data-icon="recycling">recycling</span>
    </div>
    <div>
        <h1 class="text-xl font-black text-primary font-heading tracking-tight leading-none">CleanCity</h1>
        <p class="text-[10px] font-bold text-on-surface-variant uppercase mt-0.5">Admin Portal</p>
    </div>
</div>

<nav class="flex-1 flex flex-col font-heading font-semibold text-sm overflow-y-auto">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-6 py-4 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-primary border-r-4 border-primary bg-primary/5' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
        <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
        <span>Dashboard</span>
    </a>
    
    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-6 py-4 transition-colors {{ request()->routeIs('admin.reports.*') ? 'text-primary border-r-4 border-primary bg-primary/5' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
        <span class="material-symbols-outlined" data-icon="assignment">assignment</span>
        <span>Report Management</span>
    </a>

    <a href="{{ route('admin.analytics') }}" class="flex items-center gap-3 px-6 py-4 transition-colors {{ request()->routeIs('admin.analytics') ? 'text-primary border-r-4 border-primary bg-primary/5' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
        <span class="material-symbols-outlined" data-icon="leaderboard">leaderboard</span>
        <span>Analytics Dashboard</span>
    </a>

    <a href="{{ route('admin.hotspots') }}" class="flex items-center gap-3 px-6 py-4 transition-colors {{ request()->routeIs('admin.hotspots') ? 'text-primary border-r-4 border-primary bg-primary/5' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
        <span class="material-symbols-outlined" data-icon="distance">distance</span>
        <span>Hotspot Analysis</span>
    </a>

    <a href="{{ route('admin.staff.index') }}" class="flex items-center gap-3 px-6 py-4 transition-colors {{ request()->routeIs('admin.staff.*') ? 'text-primary border-r-4 border-primary bg-primary/5' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
        <span class="material-symbols-outlined" data-icon="group">group</span>
        <span>Staff & Teams</span>
    </a>

    <a href="{{ route('admin.audit') }}" class="flex items-center gap-3 px-6 py-4 transition-colors {{ request()->routeIs('admin.audit') ? 'text-primary border-r-4 border-primary bg-primary/5' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
        <span class="material-symbols-outlined" data-icon="history">history</span>
        <span>Audit Logs</span>
    </a>
    
    <div class="mt-auto px-6 pt-4 border-t border-outline-variant/10">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center overflow-hidden">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=00482f&color=fff" alt="Admin">
            </div>
            <div>
                <p class="text-xs font-bold leading-tight">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-on-surface-variant uppercase">{{ Auth::user()->role }} Account</p>
            </div>
        </div>
    </div>
</nav>
