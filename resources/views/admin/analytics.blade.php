@extends('layouts.admin')

@section('title', 'Analytics')
@section('breadcrumb', 'Insights')

@section('content')
<div class="space-y-8">
    <x-admin.page-header
        title="Analytics Dashboard"
        description="Insights into municipal waste management performance."
    >
        <x-slot:actions>
            <button type="button" class="admin-btn-secondary py-2.5">
                <span class="material-symbols-outlined text-lg">calendar_month</span>
                Last 30 days
            </button>
            <button type="button" class="admin-btn-primary py-2.5 opacity-80 cursor-default" title="Export coming soon">
                <span class="material-symbols-outlined text-lg">download</span>
                Export
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        <x-admin.stat-card label="Resolution rate" :value="number_format($analytics['resolutionRate'], 1) . '%'" icon="analytics" accent="primary" badge="Live" />
        <x-admin.stat-card label="Avg. priority" :value="number_format($analytics['avgPriority'], 1)" icon="priority_high" accent="secondary" />
        <x-admin.stat-card label="Monthly volume" :value="$analytics['monthlyTrends']->sum('count')" icon="trending_up" accent="tertiary" />
        <x-admin.stat-card label="Categories" :value="$analytics['reportsByCategory']->count()" icon="category" accent="success" />
    </section>

    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-admin.card title="Reports by category" class="lg:col-span-1">
            <div class="relative min-h-[280px] -mt-2">
                <canvas id="categoryChart"></canvas>
            </div>
        </x-admin.card>
        <x-admin.card title="Report volume trends" class="lg:col-span-2">
            <div class="relative min-h-[280px] -mt-2">
                <canvas id="trendChart"></canvas>
            </div>
        </x-admin.card>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="admin-card p-8 flex items-center justify-between gap-6">
            <div>
                <h4 class="text-lg font-bold font-heading">Efficiency</h4>
                <p class="text-sm text-on-surface-variant mt-1 max-w-xs">Track how quickly teams resolve reported issues.</p>
                <a href="{{ route('admin.reports.index') }}" class="inline-block mt-4 text-sm font-bold text-primary hover:underline">View reports →</a>
            </div>
            <div class="w-20 h-20 rounded-full border-[6px] border-primary/15 border-t-primary flex items-center justify-center shrink-0">
                <span class="text-xl font-black text-primary">{{ number_format($analytics['resolutionRate'], 0) }}%</span>
            </div>
        </div>
        <div class="admin-card p-8 flex items-center justify-between gap-6 bg-secondary-container/10 border-secondary-container/30">
            <div>
                <h4 class="text-lg font-bold font-heading text-secondary">Priority hotspots</h4>
                <p class="text-sm text-on-surface-variant mt-1 max-w-xs">Visualize critical zones on the live map.</p>
                <a href="{{ route('admin.hotspots') }}" class="inline-block mt-4 text-sm font-bold text-secondary hover:underline">Open map →</a>
            </div>
            <div class="w-14 h-14 rounded-2xl bg-secondary-container flex items-center justify-center shrink-0 shadow-md">
                <span class="material-symbols-outlined text-on-secondary-container text-3xl">location_on</span>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts_head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('scripts')
<script>
    const chartFont = { family: 'Inter', weight: '600' };
    const colors = ['#00482f', '#006241', '#785a00', '#fec733', '#820011', '#ae001b'];

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($analytics['reportsByCategory']->pluck('label')) !!},
            datasets: [{
                data: {!! json_encode($analytics['reportsByCategory']->pluck('value')) !!},
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 12,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 16, font: chartFont }
                }
            },
            cutout: '68%',
        }
    });

    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(0, 72, 47, 0.25)');
    gradient.addColorStop(1, 'rgba(0, 72, 47, 0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($analytics['monthlyTrends']->pluck('month')) !!},
            datasets: [{
                label: 'Submissions',
                data: {!! json_encode($analytics['monthlyTrends']->pluck('count')) !!},
                borderColor: '#00482f',
                borderWidth: 3,
                fill: true,
                backgroundColor: gradient,
                tension: 0.35,
                pointBackgroundColor: '#00482f',
                pointRadius: 5,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { font: chartFont } },
                x: { grid: { display: false }, ticks: { font: chartFont } },
            }
        }
    });
</script>
@endsection
