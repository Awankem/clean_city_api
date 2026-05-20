@extends('layouts.admin')

@section('title', 'Dashboard Overview')
@section('breadcrumb', 'Overview')

@section('content')
<div class="space-y-8">
    <x-admin.page-header
        title="Dashboard Overview"
        description="Real-time status of waste management across the district."
    >
        <x-slot:actions>
            <span class="inline-flex items-center gap-2 text-sm font-semibold text-on-surface-variant bg-surface-container-lowest px-4 py-2.5 rounded-xl border border-outline-variant/15 shadow-sm">
                <span class="material-symbols-outlined text-primary text-lg">calendar_today</span>
                {{ now()->format('l, M j, Y') }}
            </span>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <x-admin.stat-card label="Total Reports" :value="number_format($stats['total'])" icon="fact_check" accent="primary" badge="Live" />
        <x-admin.stat-card label="Pending" :value="number_format($stats['pending'])" icon="pending_actions" accent="tertiary" badge="Urgent" />
        <x-admin.stat-card label="In Progress" :value="number_format($stats['in_progress'])" icon="moped" accent="secondary" />
        <x-admin.stat-card label="Resolved" :value="number_format($stats['resolved'])" icon="task_alt" accent="success" />
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <x-admin.card class="xl:col-span-2" :padding="false">
            <x-slot:header>
                <h3 class="text-lg font-bold text-on-surface font-heading">Recent Reports</h3>
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-bold text-primary inline-flex items-center gap-1 hover:underline">
                    View all <span class="material-symbols-outlined text-base">arrow_forward</span>
                </a>
            </x-slot:header>
            <div class="overflow-x-auto">
                <table class="admin-table w-full text-left">
                    <thead class="bg-surface-container-low/80">
                        <tr>
                            <th>Report ID</th>
                            <th>Citizen</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse($recentReports as $report)
                        <tr>
                            <td class="font-mono text-primary font-bold">#CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="font-medium">{{ $report->user->name ?? 'Anonymous' }}</td>
                            <td class="text-on-surface-variant">{{ $report->category->name ?? 'Uncategorized' }}</td>
                            <td><x-admin.priority-bar :score="$report->priority_score" width="w-12" /></td>
                            <td><x-admin.status-badge :status="$report->status" /></td>
                            <td class="text-right">
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="admin-btn-primary px-4 py-2 text-xs">Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-on-surface-variant text-sm">No reports yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-admin.card>

        <x-admin.card :padding="false" class="flex flex-col">
            <x-slot:header>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                        <span class="text-[10px] font-black text-primary uppercase tracking-widest">Live</span>
                    </div>
                    <h3 class="text-lg font-bold font-heading">Hotspot Map</h3>
                    <p class="text-xs text-on-surface-variant mt-0.5">Active alerts in the district</p>
                </div>
                <span class="text-xs font-bold bg-tertiary/10 text-tertiary px-3 py-1.5 rounded-full">{{ $mapReports->count() }} active</span>
            </x-slot:header>
            <div class="flex-1 relative min-h-[300px]">
                <div id="dashboard-map" class="absolute inset-0"></div>
                <div class="absolute bottom-3 left-3 bg-surface-container-lowest/95 backdrop-blur-sm px-3 py-2.5 rounded-xl border border-outline-variant/15 shadow-lg pointer-events-none text-[9px] font-bold uppercase tracking-wider text-on-surface-variant space-y-1.5">
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> Critical (&gt;7)</div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Moderate (4–7)</div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Low (&lt;4)</div>
                </div>
            </div>
            <div class="p-4 bg-surface-container-low border-t border-outline-variant/10">
                <a href="{{ route('admin.hotspots') }}" class="admin-btn-primary w-full py-3">
                    <span class="material-symbols-outlined text-lg">fullscreen</span>
                    Full map view
                </a>
            </div>
        </x-admin.card>
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-4">
        <div class="admin-card bg-primary text-on-primary p-8 flex flex-col justify-between min-h-[200px] border-0 shadow-lg shadow-primary/20">
            <div>
                <h4 class="text-lg font-bold opacity-90 font-heading">Resolution rate</h4>
                <p class="text-4xl font-black mt-2">{{ $stats['total'] > 0 ? number_format(($stats['resolved'] / $stats['total']) * 100, 1) : 0 }}%</p>
            </div>
            <p class="text-xs font-semibold bg-white/10 inline-flex self-start px-3 py-1.5 rounded-full mt-6">Based on resolved vs total reports</p>
        </div>
        <div class="lg:col-span-2 admin-card p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex-1">
                <h4 class="text-xl font-bold font-heading mb-2">Operations snapshot</h4>
                <p class="text-sm text-on-surface-variant max-w-lg leading-relaxed">Monitor pending workload and route teams using the hotspots and analytics views.</p>
                <div class="mt-6 flex flex-wrap gap-8">
                    <div>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Pending</span>
                        <p class="text-2xl font-black">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">In progress</span>
                        <p class="text-2xl font-black">{{ number_format($stats['in_progress']) }}</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('admin.analytics') }}" class="admin-btn-secondary shrink-0">
                <span class="material-symbols-outlined">leaderboard</span>
                Open analytics
            </a>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
    #dashboard-map { width: 100%; height: 100%; }
    .dash-marker { cursor: pointer; transition: transform 0.2s ease; }
    .dash-marker:hover { transform: scale(1.15); }
    .mapboxgl-popup-content {
        background: rgba(23, 28, 34, 0.95) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px !important;
        padding: 14px !important;
        color: white !important;
        box-shadow: 0 12px 32px rgba(0,0,0,0.35) !important;
        min-width: 180px;
    }
    .mapboxgl-popup-tip { display: none; }
    @keyframes dashPing {
        0%   { transform: scale(1); opacity: .7; }
        70%  { transform: scale(2.4); opacity: 0; }
        100% { transform: scale(1); opacity: 0; }
    }
</style>
@endsection

@section('scripts')
<script>
(function () {
    const TOKEN = "{{ config('services.mapbox.access_token') }}";
    if (!TOKEN || !window.mapboxgl) return;
    mapboxgl.accessToken = TOKEN;

    @php
        $mapData = $mapReports->map(fn ($r) => [
            'id' => $r->id,
            'lat' => (float) $r->latitude,
            'lng' => (float) $r->longitude,
            'priority' => (float) $r->priority_score,
            'category' => $r->category->name ?? 'Uncategorized',
        ])->values();
    @endphp
    const reports = @json($mapData);

    const map = new mapboxgl.Map({
        container: 'dashboard-map',
        style: 'mapbox://styles/mapbox/dark-v11',
        center: [-0.1870, 5.6037],
        zoom: 11,
        attributionControl: false,
    });
    map.addControl(new mapboxgl.AttributionControl({ compact: true }));

    function markerColor(p) {
        if (p > 7) return '#ef4444';
        if (p > 4) return '#fbbf24';
        return '#10b981';
    }

    map.on('load', () => {
        map.resize();
        if (!reports.length) return;
        const bounds = new mapboxgl.LngLatBounds();
        reports.forEach(r => {
            bounds.extend([r.lng, r.lat]);
            const ring = document.createElement('div');
            ring.style.cssText = `position:absolute;width:28px;height:28px;border-radius:50%;background:${markerColor(r.priority)}26;animation:dashPing 1.8s ease-out infinite;top:-14px;left:-14px;`;
            const dot = document.createElement('div');
            dot.style.cssText = `width:14px;height:14px;border-radius:50%;background:${markerColor(r.priority)};border:2px solid white;box-shadow:0 0 0 2px ${markerColor(r.priority)}66;position:relative;z-index:1;`;
            const el = document.createElement('div');
            el.className = 'dash-marker';
            el.style.cssText = 'width:14px;height:14px;position:relative;';
            el.appendChild(ring);
            el.appendChild(dot);
            const popup = new mapboxgl.Popup({ offset: 18, closeButton: false }).setHTML(`
                <p style="font-size:9px;font-weight:800;text-transform:uppercase;opacity:.5;margin-bottom:4px;">${r.category}</p>
                <p style="font-size:14px;font-weight:900;margin-bottom:6px;">#CC-${String(r.id).padStart(4,'0')}</p>
                <p style="font-size:11px;font-weight:700;color:${markerColor(r.priority)}">Priority ${r.priority.toFixed(1)}/10</p>
            `);
            new mapboxgl.Marker(el).setLngLat([r.lng, r.lat]).setPopup(popup).addTo(map);
        });
        if (reports.length > 1) map.fitBounds(bounds, { padding: 48, maxZoom: 14, duration: 800 });
        else map.flyTo({ center: [reports[0].lng, reports[0].lat], zoom: 14, duration: 800 });
    });
    setTimeout(() => map.resize(), 300);
})();
</script>
@endsection
