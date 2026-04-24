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
            <div class="flex-1 flex flex-col justify-center space-y-6">
                @foreach($analytics['reportsByCategory'] as $category)
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm font-bold">
                            <span class="text-on-surface-variant">{{ $category->label }}</span>
                            <span class="text-on-surface">{{ $category->value }}</span>
                        </div>
                        <div class="h-3 w-full bg-surface-container rounded-full overflow-hidden">
                            @php $percent = ($category->value / max(1, $analytics['reportsByCategory']->sum('value'))) * 100; @endphp
                            <div class="h-full bg-primary rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Monthly Trend Chart -->
        <div class="lg:col-span-2 bg-surface-container-lowest p-8 rounded-3xl border border-outline-variant/10 shadow-sm flex flex-col">
            <h3 class="text-xl font-bold text-on-surface font-heading mb-6">Report Volume Trends (Submissions)</h3>
            <div class="flex-1 flex items-end justify-between gap-2 min-h-[300px] pt-8">
                @php $maxCount = max(1, $analytics['monthlyTrends']->max('count')); @endphp
                @foreach($analytics['monthlyTrends'] as $trend)
                    <div class="flex-1 flex flex-col items-center gap-4 group">
                        <div class="relative w-full flex justify-center">
                            <div class="w-12 bg-primary/10 group-hover:bg-primary/20 transition-colors rounded-t-xl" style="height: {{ ($trend->count / $maxCount) * 250 }}px"></div>
                            <div class="absolute -top-8 opacity-0 group-hover:opacity-100 transition-opacity bg-on-surface text-surface text-[10px] font-black px-2 py-1 rounded shadow-lg">
                                {{ $trend->count }}
                            </div>
                        </div>
                        <span class="text-xs font-black text-on-surface-variant uppercase tracking-tighter">{{ $trend->month }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Secondary Insights -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
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
