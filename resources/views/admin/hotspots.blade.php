@extends('layouts.admin')

@section('title', 'Hotspot Analysis')

@section('content')
<div class="space-y-8 h-[calc(100vh-140px)] flex flex-col">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
        <div>
            <h2 class="text-3xl font-black text-on-surface font-heading tracking-tight">Hotspot Analysis</h2>
            <p class="text-on-surface-variant font-medium">Geospatial visualization of data density and report clusters.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex bg-surface-container-high rounded-xl p-1 border border-outline-variant/10">
                <button class="px-4 py-2 rounded-lg text-xs font-black bg-primary text-on-primary shadow-sm">HEATMAP</button>
                <button class="px-4 py-2 rounded-lg text-xs font-black text-on-surface hover:bg-surface-container transition-colors">CLUSTERS</button>
            </div>
            <button class="bg-surface-container-high text-on-surface p-3 rounded-xl border border-outline-variant/10">
                <span class="material-symbols-outlined text-sm">filter_list</span>
            </button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="flex-1 relative bg-surface-container-low rounded-[40px] overflow-hidden border border-outline-variant/20 shadow-inner">
        <!-- Mock Map Background -->
        <div class="absolute inset-0 grayscale opacity-40 bg-[url('https://api.mapbox.com/styles/v1/mapbox/light-v10/static/0,0,1,0,0/1280x800?access_token=pk.placeholder')] bg-cover bg-center"></div>
        
        <!-- Hotspot Overlays (Mocked) -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/4 left-1/3 w-64 h-64 bg-primary/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-1/3 right-1/4 w-48 h-48 bg-secondary/15 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-80 h-80 bg-tertiary/10 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
        </div>

        <!-- Float Info Panel -->
        <div class="absolute top-8 left-8 w-80 bg-surface-container-lowest/90 backdrop-blur-md p-6 rounded-3xl border border-outline-variant/10 shadow-2xl z-10">
            <h4 class="text-sm font-black text-primary uppercase tracking-widest mb-4">Critical Zones</h4>
            <div class="space-y-4">
                @foreach($hotspots->take(3) as $spot)
                    <div class="flex items-center gap-3 p-3 bg-surface-container rounded-2xl border border-outline-variant/5">
                        <div class="w-2 h-10 @if($spot->priority_score > 7) bg-error @else bg-secondary @endif rounded-full"></div>
                        <div class="flex-1">
                            <p class="text-[10px] font-bold text-on-surface-variant uppercase">{{ $spot->category->name }}</p>
                            <p class="text-sm font-black text-on-surface">Sector {{ $loop->iteration + 3 }} Cluster</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-on-surface">{{ $spot->priority_score }} pt</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="w-full mt-6 py-3 border-2 border-outline-variant/10 rounded-2xl text-xs font-black text-on-surface-variant hover:bg-surface-container transition-all">
                VIEW FULL REPORT LIST
            </button>
        </div>

        <!-- Map Controls -->
        <div class="absolute bottom-8 right-8 flex flex-col gap-2 z-10">
            <button class="w-12 h-12 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/10 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined">add</span>
            </button>
            <button class="w-12 h-12 bg-surface-container-lowest rounded-2xl shadow-xl border border-outline-variant/10 flex items-center justify-center text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined">remove</span>
            </button>
            <div class="h-4"></div>
            <button class="w-12 h-12 bg-primary rounded-2xl shadow-xl shadow-primary/20 flex items-center justify-center text-on-primary">
                <span class="material-symbols-outlined">my_location</span>
            </button>
        </div>

        <!-- Legend -->
        <div class="absolute bottom-8 left-8 bg-surface-container-lowest/90 backdrop-blur-md px-6 py-3 rounded-2xl border border-outline-variant/10 shadow-lg z-10 flex items-center gap-6">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-error"></span>
                <span class="text-[10px] font-black text-on-surface-variant uppercase">Critical</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-secondary"></span>
                <span class="text-[10px] font-black text-on-surface-variant uppercase">Severe</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-primary"></span>
                <span class="text-[10px] font-black text-on-surface-variant uppercase">Normal</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Custom map aesthetics */
    body { overflow-y: hidden; }
</style>
@endsection
