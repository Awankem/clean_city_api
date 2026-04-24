@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-black text-primary font-heading tracking-tight">Dashboard Overview</h2>
            <p class="text-sm text-on-surface-variant">Real-time status of waste management in the district</p>
        </div>
        <div class="text-sm font-medium text-on-surface-variant bg-surface-container-lowest px-4 py-2 rounded-lg border border-outline-variant/10 shadow-sm">
            {{ now()->format('M d, Y') }}
        </div>
    </div>

    <!-- Summary Stats -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Reports -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border-b-4 border-primary transition-transform hover:scale-[1.02]">
            <div class="flex justify-between items-start mb-4">
                <span class="p-3 bg-primary/10 rounded-xl">
                    <span class="material-symbols-outlined text-primary" data-icon="fact_check">fact_check</span>
                </span>
                <span class="text-xs font-bold text-primary px-2 py-1 bg-primary/5 rounded">Live</span>
            </div>
            <p class="text-on-surface-variant text-sm font-medium mb-1">Total Reports</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ number_format($stats['total']) }}</h3>
        </div>

        <!-- Pending -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border-b-4 border-tertiary transition-transform hover:scale-[1.02]">
            <div class="flex justify-between items-start mb-4">
                <span class="p-3 bg-tertiary/10 rounded-xl">
                    <span class="material-symbols-outlined text-tertiary" data-icon="pending_actions">pending_actions</span>
                </span>
                <span class="text-xs font-bold text-tertiary px-2 py-1 bg-tertiary/5 rounded">Urgent</span>
            </div>
            <p class="text-on-surface-variant text-sm font-medium mb-1">Pending</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ number_format($stats['pending']) }}</h3>
        </div>

        <!-- In Progress -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border-b-4 border-secondary transition-transform hover:scale-[1.02]">
            <div class="flex justify-between items-start mb-4">
                <span class="p-3 bg-secondary/10 rounded-xl">
                    <span class="material-symbols-outlined text-secondary" data-icon="moped">moped</span>
                </span>
            </div>
            <p class="text-on-surface-variant text-sm font-medium mb-1">In Progress</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ number_format($stats['in_progress']) }}</h3>
        </div>

        <!-- Resolved -->
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border-b-4 border-primary-container transition-transform hover:scale-[1.02]">
            <div class="flex justify-between items-start mb-4">
                <span class="p-3 bg-primary-container/10 rounded-xl">
                    <span class="material-symbols-outlined text-primary-container" data-icon="task_alt">task_alt</span>
                </span>
            </div>
            <p class="text-on-surface-variant text-sm font-medium mb-1">Resolved</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ number_format($stats['resolved']) }}</h3>
        </div>
    </section>

    <!-- Main Grid: Table & Map -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Reports Table -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border border-outline-variant/10">
            <div class="p-6 border-b border-outline-variant/10 flex justify-between items-center">
                <h3 class="text-xl font-bold text-on-surface font-heading">Recent Reports</h3>
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-bold text-primary flex items-center gap-1 hover:underline">
                    View All <span class="material-symbols-outlined text-sm" data-icon="arrow_forward">arrow_forward</span>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Report ID</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Citizen</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        @foreach($recentReports as $report)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-4 text-sm font-mono text-primary font-bold">#CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 text-sm font-medium">{{ $report->user->name ?? 'Anonymous' }}</td>
                            <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $report->category->name ?? 'Uncategorized' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-12 bg-surface-container-highest h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full bg-{{ $report->priority_score > 7 ? 'tertiary' : ($report->priority_score > 4 ? 'secondary' : 'primary') }}" style="width: {{ $report->priority_score * 10 }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-on-surface">{{ number_format($report->priority_score, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-tertiary-container text-on-tertiary text-white',
                                        'in_progress' => 'bg-secondary-container text-on-secondary',
                                        'resolved' => 'bg-primary-container text-on-primary',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusColors[$report->status] ?? 'bg-surface-container-high' }}">
                                    {{ str_replace('_', ' ', $report->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="bg-primary text-on-primary px-4 py-2 rounded-lg text-xs font-bold transition-all active:scale-95 shadow-sm inline-block">Manage</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Hotspot Map Widget -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden flex flex-col h-full border border-outline-variant/10">
            <div class="p-6 border-b border-outline-variant/10">
                <h3 class="text-xl font-bold text-on-surface font-heading">Hotspot Map</h3>
                <p class="text-xs text-on-surface-variant mt-1">Live coverage of urgent alerts in the city</p>
            </div>
            <div class="flex-1 relative min-h-[300px] bg-surface-container-low flex items-center justify-center">
                <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuC_TttPuXg_oGhQEymjMPdSQI-YqtfkR84TFTIY9hJhSDpzEgtLUmQE_Gs21WEWi4KywW3t3H_Vorw-aOkqZJRszEjAb_T1flVDIHSuEqt5KEEFDMoqDE8oywqdwphOXspk-HKA0snvlQLp_fCVX3LABxd1bjYIWsB7Z7QfPvo5-YXouqtJR3k9bS-0OQ8mB8oLMW6gmvqesF9x-MUcNSnbNjTSukirINTbHrNF1K-abwIpV9lUUln6uaCIdB6u4UvIKwiYV3AsAvD9" alt="Map View" class="w-full h-full object-cover">
                <div class="absolute top-1/4 left-1/3 w-8 h-8">
                    <div class="absolute inset-0 bg-tertiary rounded-full animate-ping opacity-75"></div>
                    <div class="absolute inset-2 bg-tertiary rounded-full border-2 border-white shadow-lg"></div>
                </div>
            </div>
            <div class="p-4 bg-surface-container-low">
                <button class="w-full bg-primary text-on-primary py-3 rounded-xl text-sm font-bold flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                    <span class="material-symbols-outlined text-sm" data-icon="fullscreen">fullscreen</span>
                    Enter Map View
                </button>
            </div>
        </div>
    </section>

    <!-- Secondary Stats -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
        <div class="bg-primary p-8 rounded-2xl text-on-primary flex flex-col justify-between min-h-[200px]">
            <div>
                <h4 class="text-lg font-bold opacity-80 mb-2 font-heading">Efficiency Rating</h4>
                <p class="text-4xl font-black">94.2%</p>
            </div>
            <div class="flex items-end justify-between">
                <div class="flex gap-1 h-12 items-end">
                    <div class="w-2 bg-white/20 h-1/2 rounded-full"></div>
                    <div class="w-2 bg-white/40 h-3/4 rounded-full"></div>
                    <div class="w-2 bg-white/20 h-2/3 rounded-full"></div>
                    <div class="w-2 bg-white h-full rounded-full"></div>
                    <div class="w-2 bg-white/60 h-4/5 rounded-full"></div>
                </div>
                <span class="text-xs font-bold bg-white/10 px-3 py-1 rounded-full">+2.4 pts this week</span>
            </div>
        </div>
        <div class="lg:col-span-2 bg-surface-container-lowest p-8 rounded-2xl shadow-sm flex items-center justify-between gap-8 border border-outline-variant/10">
            <div class="flex-1">
                <h4 class="text-xl font-bold text-on-surface mb-2 font-heading">Resource Allocation</h4>
                <p class="text-on-surface-variant text-sm max-w-md">Currently optimizing waste collection routes based on live traffic and report volume in the central districts.</p>
                <div class="mt-6 flex gap-8">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Active Units</span>
                        <span class="text-2xl font-black text-on-surface">24/30</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-on-surface-variant uppercase tracking-widest">Est. Response</span>
                        <span class="text-2xl font-black text-on-surface">14 min</span>
                    </div>
                </div>
            </div>
            <div class="w-32 h-32 hidden md:block">
                <svg class="w-full h-full transform -rotate-90" viewbox="0 0 36 36">
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#e4e8f1" stroke-width="3"></circle>
                    <circle cx="18" cy="18" r="15.9155" fill="none" stroke="#00482f" stroke-width="3" stroke-dasharray="80, 100"></circle>
                    <text x="18" y="20.35" fill="#171c22" text-anchor="middle" transform="rotate(90 18 18)" class="font-black text-[8px]">80%</text>
                </svg>
            </div>
        </div>
    </section>
</div>
@endsection
