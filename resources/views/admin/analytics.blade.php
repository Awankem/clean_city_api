@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-on-surface font-heading tracking-tight">Analytics Dashboard</h2>
            <p class="text-on-surface-variant font-medium">Real-time insights into municipal waste management performance.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-surface-container-high text-on-surface px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 border border-outline-variant/10">
                <span class="material-symbols-outlined text-sm">calendar_month</span>
                Last 30 Days
            </button>
            <button class="bg-primary text-on-primary px-6 py-2 rounded-xl text-sm font-black shadow-lg shadow-primary/20">
                Export Report
            </button>
        </div>
    </div>

    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Resolution Rate -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">analytics</span>
                </div>
                <span class="text-[10px] font-black text-primary bg-primary/5 px-2 py-1 rounded-full">+12.5%</span>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">Resolution Rate</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ number_format($analytics['resolutionRate'], 1) }}%</h3>
        </div>

        <!-- Avg Priority -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary">
                    <span class="material-symbols-outlined">priority_high</span>
                </div>
                <span class="text-[10px] font-black text-secondary bg-secondary/5 px-2 py-1 rounded-full">Elevated</span>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">Avg. Priority Score</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ number_format($analytics['avgPriority'], 1) }}</h3>
        </div>

        <!-- Monthly Volume -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-tertiary/10 flex items-center justify-center text-tertiary">
                    <span class="material-symbols-outlined">trending_up</span>
                </div>
                <span class="text-[10px] font-black text-tertiary bg-tertiary/5 px-2 py-1 rounded-full">Stable</span>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">Monthly Reports</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ $analytics['monthlyTrends']->sum('count') }}</h3>
        </div>

        <!-- Categories -->
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">category</span>
                </div>
            </div>
            <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1">Active Categories</p>
            <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ $analytics['reportsByCategory']->count() }}</h3>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Reports by Category -->
        <div class="lg:col-span-1 bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/10 shadow-sm flex flex-col h-full">
            <h3 class="text-xl font-bold text-on-surface font-heading mb-6">Reports by Category</h3>
            <div class="flex-1 relative min-h-[300px]">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="lg:col-span-2 bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/10 shadow-sm flex flex-col">
            <h3 class="text-xl font-bold text-on-surface font-heading mb-6">Report Volume Trends</h3>
            <div class="flex-1 relative min-h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Secondary Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <div class="bg-surface-container-low p-8 rounded-3xl border border-outline-variant/10 flex items-center justify-between">
            <div class="space-y-2">
                <h4 class="text-lg font-bold text-on-surface font-heading">Efficiency Score</h4>
                <p class="text-sm text-on-surface-variant max-w-xs">Your teams are resolving issues 15% faster than last quarter.</p>
                <a href="#" class="inline-block text-sm font-black text-primary uppercase tracking-widest pt-2 hover:underline">View Team Metrics →</a>
            </div>
            <div class="w-24 h-24 rounded-full border-8 border-primary/20 border-t-primary flex items-center justify-center">
                <span class="text-xl font-black text-primary">84%</span>
            </div>
        </div>

        <div class="bg-secondary/5 p-8 rounded-3xl border border-secondary/10 flex items-center justify-between">
            <div class="space-y-2">
                <h4 class="text-lg font-bold text-secondary-container text-on-secondary-container font-heading">Priority Hotspots</h4>
                <p class="text-sm text-on-secondary-container/80 max-w-xs">Critical issues detected in Sector 4. Immediate intervention recommended.</p>
                <a href="{{ route('admin.hotspots') }}" class="inline-block text-sm font-black text-secondary uppercase tracking-widest pt-2 hover:underline">Open Map View →</a>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-secondary flex items-center justify-center shadow-lg shadow-secondary/20">
                <span class="material-symbols-outlined text-on-secondary text-3xl">location_on</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts_head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('scripts')
<script>
    // Category Doughnut Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($analytics['reportsByCategory']->pluck('label')) !!},
            datasets: [{
                data: {!! json_encode($analytics['reportsByCategory']->pluck('value')) !!},
                backgroundColor: [
                    '#00482f', // primary
                    '#52634f', // secondary
                    '#38656a', // tertiary
                    '#006d3a',
                    '#0052d1'
                ],
                borderWidth: 0,
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { family: 'Inter', weight: 'bold' }
                    }
                }
            },
            cutout: '70%'
        }
    });

    // Trend Line Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 72, 47, 0.2)');
    gradient.addColorStop(1, 'rgba(0, 72, 47, 0)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($analytics['monthlyTrends']->pluck('month')) !!},
            datasets: [{
                label: 'Submissions',
                data: {!! json_encode($analytics['monthlyTrends']->pluck('count')) !!},
                borderColor: '#00482f',
                borderWidth: 4,
                fill: true,
                backgroundColor: gradient,
                tension: 0.4,
                pointBackgroundColor: '#00482f',
                pointRadius: 6,
                pointHoverRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: { font: { weight: 'bold' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold' } }
                }
            }
        }
    });
</script>
@endsection

