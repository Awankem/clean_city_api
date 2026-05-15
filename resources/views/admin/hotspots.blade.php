@extends('layouts.admin')

@section('title', 'Hotspot Analysis')

@section('scripts_head')
    <!-- Socket.io / Echo Support -->
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
@endsection

@section('content')
<div class="analysis-container flex flex-col gap-6" x-data="hotspotApp()">
    <!-- Premium Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 shrink-0">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Live Geospatial Intelligence</span>
            </div>
            <h2 class="text-4xl font-black text-on-surface font-heading tracking-tight">Hotspot Analysis</h2>
            <p class="text-on-surface-variant font-medium mt-1">Strategic visualization of municipal sanitation hotspots and resource distribution.</p>
        </div>
        
        <div class="flex items-center gap-4 bg-surface-container-low p-2 rounded-[24px] border border-outline-variant/10 shadow-sm">
            <div class="flex bg-surface-container-high rounded-2xl p-1">
                <button 
                    @click="viewMode = 'heatmap'"
                    :class="viewMode === 'heatmap' ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'text-on-surface hover:bg-surface-container'"
                    class="px-6 py-2.5 rounded-xl text-xs font-black transition-all active:scale-95">HEATMAP</button>
                <button 
                    @click="viewMode = 'markers'"
                    :class="viewMode === 'markers' ? 'bg-primary text-on-primary shadow-lg shadow-primary/20' : 'text-on-surface hover:bg-surface-container'"
                    class="px-6 py-2.5 rounded-xl text-xs font-black transition-all">MARKERS</button>
            </div>
            <div class="h-8 w-[1px] bg-outline-variant/20"></div>
            <button @click="showFilters = !showFilters" :class="showFilters ? 'bg-primary text-on-primary' : 'bg-surface-container-highest text-on-surface'" class="p-3 rounded-2xl border border-outline-variant/10 hover:opacity-90 transition-all active:scale-90 group">
                <span class="material-symbols-outlined text-xl group-hover:rotate-180 transition-transform duration-500">tune</span>
            </button>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 flex gap-6 min-h-0">
        <!-- Left Sidebar: Analytics & Filters -->
        <div class="w-80 flex flex-col gap-6 shrink-0 overflow-y-auto pr-2 custom-scrollbar" x-show="!showFilters" x-transition>
            <!-- Intelligence Summary -->
            <div class="bg-surface-container-lowest/80 backdrop-blur-xl p-6 rounded-[32px] border border-outline-variant/10 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-xs font-black text-primary uppercase tracking-widest">Active Intelligence</h4>
                    <span class="text-[10px] font-bold text-on-surface-variant bg-surface-container px-2 py-1 rounded-full">Live Feed</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-surface-container p-4 rounded-2xl border border-outline-variant/5">
                        <p class="text-[10px] font-bold text-on-surface-variant uppercase mb-1">Visible</p>
                        <p class="text-2xl font-black text-on-surface" x-text="filteredHotspots.length">0</p>
                    </div>
                    <div class="bg-error/5 p-4 rounded-2xl border border-error/10">
                        <p class="text-[10px] font-bold text-error uppercase mb-1">Critical</p>
                        <p class="text-2xl font-black text-error" x-text="criticalCount">0</p>
                    </div>
                </div>
            </div>

            <!-- Priority Sectors List -->
            <div class="flex-1 bg-surface-container-lowest/80 backdrop-blur-xl p-6 rounded-[32px] border border-outline-variant/10 shadow-xl flex flex-col min-h-0">
                <h4 class="text-xs font-black text-on-surface-variant uppercase tracking-widest mb-6">Priority Sectors</h4>
                <div class="space-y-4 overflow-y-auto flex-1 pr-2 custom-scrollbar">
                    <template x-for="(spot, index) in filteredHotspots.slice(0, 10)" :key="spot.id">
                        <div @click="flyTo(spot)" class="group flex items-center gap-4 p-4 bg-surface-container rounded-2xl border border-outline-variant/5 hover:border-primary/30 transition-all cursor-pointer">
                            <div class="relative">
                                <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center overflow-hidden">
                                    <span class="material-symbols-outlined" :class="spot.priority > 7 ? 'text-error' : (spot.priority > 4 ? 'text-secondary' : 'text-primary')" x-text="spot.priority > 7 ? 'warning' : 'location_on'"></span>
                                </div>
                                <div x-show="spot.priority > 7" class="absolute -top-1 -right-1 w-3 h-3 bg-error rounded-full border-2 border-white animate-ping"></div>
                            </div>
                            <div class="flex-1">
                                <p class="text-[10px] font-bold text-on-surface-variant uppercase" x-text="spot.category"></p>
                                <p class="text-sm font-black text-on-surface truncate" x-text="spot.location_name || 'Unknown Location'"></p>
                                <div class="flex items-center gap-1 mt-1">
                                    <span class="text-[9px] font-medium text-on-surface-variant">Priority:</span>
                                    <span class="text-[9px] font-black text-on-surface" x-text="spot.priority"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Filter Sidebar (Toggled) -->
        <div class="w-80 flex flex-col gap-6 shrink-0 overflow-y-auto pr-2 custom-scrollbar" x-show="showFilters" x-transition>
            <div class="bg-surface-container-lowest/80 backdrop-blur-xl p-6 rounded-[32px] border border-outline-variant/10 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h4 class="text-xs font-black text-primary uppercase tracking-widest">Map Filters</h4>
                    <button @click="resetFilters()" class="text-[10px] font-bold text-primary hover:underline uppercase">Reset</button>
                </div>

                <!-- Status Filter -->
                <div class="mb-8">
                    <h5 class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-4">Report Status</h5>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" value="pending" x-model="filters.status" class="w-5 h-5 rounded-lg border-outline-variant text-primary focus:ring-primary/20">
                            <span class="text-sm font-bold text-on-surface group-hover:text-primary transition-colors uppercase tracking-tight">Pending</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" value="in_progress" x-model="filters.status" class="w-5 h-5 rounded-lg border-outline-variant text-secondary focus:ring-secondary/20">
                            <span class="text-sm font-bold text-on-surface group-hover:text-secondary transition-colors uppercase tracking-tight">In Progress</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" value="resolved" x-model="filters.status" class="w-5 h-5 rounded-lg border-outline-variant text-success focus:ring-success/20">
                            <span class="text-sm font-bold text-on-surface group-hover:text-success transition-colors uppercase tracking-tight">Resolved</span>
                        </label>
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <h5 class="text-[11px] font-black text-on-surface-variant uppercase tracking-widest mb-4">Categories</h5>
                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($categories as $cat)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" value="{{ $cat->name }}" x-model="filters.categories" class="w-5 h-5 rounded-lg border-outline-variant text-primary focus:ring-primary/20">
                                <span class="text-sm font-bold text-on-surface group-hover:text-primary transition-colors">{{ $cat->name }}</span>
                            </div>
                            <span class="text-[10px] font-black bg-surface-container px-2 py-1 rounded-full text-on-surface-variant">{{ $cat->reports_count }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Workspace: Map Explorer -->
        <div class="flex-1 relative bg-surface-container-low rounded-[48px] overflow-hidden border border-outline-variant/20 shadow-2xl">
            <!-- Map Instance -->
            <div id="map" class="absolute inset-0"></div>
            
            <!-- Floating Map Interface -->
            <div class="absolute inset-0 pointer-events-none flex flex-col justify-between p-8">
                <!-- Top HUD -->
                <div class="flex justify-between items-start pointer-events-none">
                    <div class="flex flex-col gap-2 pointer-events-auto">
                        <div class="bg-surface-container-lowest/90 backdrop-blur-md px-6 py-4 rounded-[24px] border border-outline-variant/10 shadow-2xl">
                            <div class="flex items-center gap-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-primary uppercase tracking-tighter">Current District</span>
                                    <span class="text-sm font-black text-on-surface">Central Metropolitan Area</span>
                                </div>
                                <div class="w-[1px] h-8 bg-outline-variant/20"></div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-secondary uppercase tracking-tighter">Visibility</span>
                                    <span class="text-sm font-black text-on-surface" x-text="Math.round((filteredHotspots.length / {{ $hotspots->count() || 1 }}) * 100) + '% Filtered'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Styles -->
                    <div class="bg-surface-container-lowest/90 backdrop-blur-md p-2 rounded-[24px] border border-outline-variant/10 shadow-2xl pointer-events-auto flex gap-1">
                        <button @click="setMapStyle('dark-v11')" :class="mapStyle === 'dark-v11' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant'" class="p-3 rounded-2xl hover:bg-surface-container transition-colors" title="Dark">
                            <span class="material-symbols-outlined">dark_mode</span>
                        </button>
                        <button @click="setMapStyle('satellite-streets-v12')" :class="mapStyle === 'satellite-streets-v12' ? 'bg-primary/10 text-primary' : 'text-on-surface-variant'" class="p-3 rounded-2xl hover:bg-surface-container transition-colors" title="Satellite">
                            <span class="material-symbols-outlined">satellite</span>
                        </button>
                        <button @click="toggleRecurrenceZones()" :class="showRecurrence ? 'bg-error/10 text-error' : 'text-on-surface-variant'" class="p-3 rounded-2xl hover:bg-surface-container transition-colors" title="Recurrence Zones">
                            <span class="material-symbols-outlined">history</span>
                        </button>
                    </div>
                </div>

                <!-- Bottom HUD -->
                <div class="flex justify-between items-end pointer-events-none">
                    <!-- Legend Overlay -->
                    <div class="bg-surface-container-lowest/95 backdrop-blur-xl px-8 py-4 rounded-[32px] border border-outline-variant/10 shadow-2xl pointer-events-auto flex items-center gap-10">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-error shadow-[0_0_12px_rgba(255,82,82,0.4)]"></span>
                            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Critical Alert</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-secondary shadow-[0_0_12px_rgba(255,183,77,0.4)]"></span>
                            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Moderate</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-primary shadow-[0_0_12px_rgba(76,175,80,0.4)]"></span>
                            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Normal</span>
                        </div>
                    </div>

                    <!-- Enhanced Controls -->
                    <div class="flex flex-col gap-3 pointer-events-auto">
                        <div class="flex flex-col bg-surface-container-lowest/90 backdrop-blur-md rounded-[24px] border border-outline-variant/10 shadow-2xl overflow-hidden">
                            <button class="p-4 hover:bg-surface-container transition-colors text-on-surface-variant border-b border-outline-variant/10" id="zoom-in">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                            <button class="p-4 hover:bg-surface-container transition-colors text-on-surface-variant" id="zoom-out">
                                <span class="material-symbols-outlined">remove</span>
                            </button>
                        </div>
                        <button @click="centerOnCity()" class="w-14 h-14 bg-primary text-on-primary rounded-[24px] shadow-2xl shadow-primary/40 flex items-center justify-center transition-transform active:scale-90 hover:rotate-12">
                            <span class="material-symbols-outlined text-2xl">my_location</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .analysis-container {
        height: calc(100vh - 140px);
        min-height: 600px;
    }
    
    #map { width: 100%; height: 100%; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(var(--primary-rgb), 0.1);
        border-radius: 10px;
    }

    .mapboxgl-popup { z-index: 100; }
    .mapboxgl-popup-content {
        background: rgba(23, 28, 34, 0.95) !important;
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px !important;
        padding: 0 !important; /* Managed by internal div */
        color: white !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4) !important;
        overflow: hidden;
        max-width: 280px !important;
    }
    .mapboxgl-popup-close-button {
        color: white !important;
        font-size: 1.5rem !important;
        padding: 10px !important;
        z-index: 10;
    }

    .custom-marker { cursor: pointer; }
    .marker-pulse {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        animation: pulse-ring 2s infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.5); opacity: 0.8; }
        100% { transform: scale(2.5); opacity: 0; }
    }
</style>
@endsection

@section('scripts')
<script>
    @php
        $hotspotData = $hotspots->map(function($h) {
            return [
                'id' => $h->id,
                'lat' => (float)$h->latitude,
                'lng' => (float)$h->longitude,
                'priority' => (float)$h->priority_score,
                'category' => $h->category->name ?? 'Uncategorized',
                'status' => $h->status,
                'location_name' => $h->location_name,
                'image' => $h->images->first() ? asset('storage/' . $h->images->first()->image_path) : null,
                'created_at' => $h->created_at->format('M d, Y')
            ];
        })->values();
    @endphp

    function hotspotApp() {
        return {
            viewMode: 'heatmap',
            showFilters: false,
            mapStyle: 'dark-v11',
            showRecurrence: false,
            filters: {
                status: ['pending', 'in_progress'],
                categories: []
            },
            allHotspots: @json($hotspotData),
            map: null,
            markers: [],

            get filteredHotspots() {
                return this.allHotspots.filter(h => {
                    const statusMatch = this.filters.status.includes(h.status);
                    const categoryMatch = this.filters.categories.length === 0 || this.filters.categories.includes(h.category);
                    return statusMatch && categoryMatch;
                });
            },

            get criticalCount() {
                return this.filteredHotspots.filter(h => h.priority > 7).length;
            },

            init() {
                this.initMap();
                this.$watch('filters', () => this.updateMapData(), { deep: true });
                this.$watch('viewMode', () => this.toggleViewMode());
                
                // Real-time updates
                window.Pusher = Pusher;
                window.Echo = new Echo({
                    broadcaster: 'reverb',
                    key: "{{ env('REVERB_APP_KEY', 'clean_city_key') }}",
                    wsHost: "{{ env('REVERB_HOST', '127.0.0.1') }}",
                    wsPort: {{ env('REVERB_PORT', 8080) }},
                    forceTLS: false,
                    enabledTransports: ['ws', 'wss'],
                });

                window.Echo.channel('hotspots').listen('.report.submitted', (data) => {
                    const newReport = {
                        id: data.report.id,
                        lat: parseFloat(data.report.latitude),
                        lng: parseFloat(data.report.longitude),
                        priority: parseFloat(data.report.priority_score),
                        category: data.report.category?.name || 'Uncategorized',
                        status: data.report.status,
                        location_name: data.report.location_name,
                        image: null, // New ones might not have images yet in real-time payload
                        created_at: 'Just now'
                    };
                    this.allHotspots.unshift(newReport);
                    this.updateMapData();
                });
            },

            initMap() {
                mapboxgl.accessToken = "{{ config('services.mapbox.access_token') }}";
                this.map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/' + this.mapStyle,
                    center: [-0.1870, 5.6037],
                    zoom: 12,
                    pitch: 45,
                    antialias: true
                });

                document.getElementById('zoom-in').onclick = () => this.map.zoomIn();
                document.getElementById('zoom-out').onclick = () => this.map.zoomOut();

                this.map.on('load', () => {
                    this.map.addSource('hotspots-source', {
                        type: 'geojson',
                        data: this.getGeoJSON(),
                        cluster: true,
                        clusterMaxZoom: 14,
                        clusterRadius: 50
                    });

                    // Heatmap Layer
                    this.map.addLayer({
                        id: 'hotspots-heatmap',
                        type: 'heatmap',
                        source: 'hotspots-source',
                        maxzoom: 15,
                        paint: {
                            'heatmap-weight': ['interpolate', ['linear'], ['get', 'priority'], 0, 0, 10, 1],
                            'heatmap-intensity': ['interpolate', ['linear'], ['zoom'], 0, 1, 15, 3],
                            'heatmap-color': [
                                'interpolate', ['linear'], ['heatmap-density'],
                                0, 'rgba(33,102,172,0)',
                                0.2, 'rgb(103,169,207)',
                                0.4, 'rgb(209,229,240)',
                                0.6, 'rgb(253,219,199)',
                                0.8, 'rgb(239,138,98)',
                                1, 'rgb(178,24,43)'
                            ],
                            'heatmap-radius': ['interpolate', ['linear'], ['zoom'], 0, 2, 15, 20],
                            'heatmap-opacity': 1
                        }
                    });

                    // Recurrence Zones (Chronic areas)
                    this.map.addLayer({
                        id: 'recurrence-zones',
                        type: 'circle',
                        source: 'hotspots-source',
                        filter: ['has', 'point_count'],
                        layout: { 'visibility': 'none' },
                        paint: {
                            'circle-color': '#ff5252',
                            'circle-opacity': 0.3,
                            'circle-radius': ['interpolate', ['linear'], ['get', 'point_count'], 1, 20, 10, 60],
                            'circle-stroke-width': 2,
                            'circle-stroke-color': '#ff5252'
                        }
                    });

                    this.toggleViewMode();
                    this.renderMarkers();
                });
            },

            getGeoJSON() {
                return {
                    type: 'FeatureCollection',
                    features: this.filteredHotspots.map(h => ({
                        type: 'Feature',
                        properties: { 
                            id: h.id, 
                            priority: h.priority, 
                            category: h.category,
                            status: h.status
                        },
                        geometry: { type: 'Point', coordinates: [h.lng, h.lat] }
                    }))
                };
            },

            updateMapData() {
                const source = this.map.getSource('hotspots-source');
                if (source) {
                    source.setData(this.getGeoJSON());
                }
                this.renderMarkers();
            },

            renderMarkers() {
                this.markers.forEach(m => m.remove());
                this.markers = [];

                if (this.viewMode === 'heatmap') return;

                this.filteredHotspots.forEach(h => {
                    const el = document.createElement('div');
                    el.className = 'custom-marker';
                    const color = h.priority > 7 ? '#ff5252' : (h.priority > 4 ? '#ffb74d' : '#4caf50');
                    
                    el.innerHTML = `
                        <div class="relative flex items-center justify-center" style="width: 24px; height: 24px;">
                            <div class="marker-pulse" style="background: ${color}"></div>
                            <div class="relative w-4 h-4 rounded-full border-2 border-white shadow-lg" style="background: ${color}"></div>
                        </div>
                    `;

                    const popup = new mapboxgl.Popup({ offset: 25 }).setHTML(this.getPopupHTML(h));

                    const marker = new mapboxgl.Marker(el)
                        .setLngLat([h.lng, h.lat])
                        .setPopup(popup)
                        .addTo(this.map);
                    
                    this.markers.push(marker);
                });
            },

            getPopupHTML(h) {
                const statusLabel = h.status.replace('_', ' ').toUpperCase();
                const statusColor = h.status === 'pending' ? 'error' : (h.status === 'in_progress' ? 'secondary' : 'primary');
                
                return `
                    <div class="flex flex-col">
                        ${h.image ? `<img src="${h.image}" class="w-full h-32 object-cover" alt="Report image">` : 
                                    `<div class="w-full h-32 bg-surface-container-high flex items-center justify-center">
                                        <span class="material-symbols-outlined text-4xl opacity-20 text-on-surface">image_not_supported</span>
                                     </div>`}
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] font-black uppercase tracking-widest text-on-surface-variant">${h.category}</span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full bg-${statusColor}/20 text-${statusColor}">${statusLabel}</span>
                            </div>
                            <h4 class="text-sm font-black text-on-surface leading-tight">${h.location_name || 'Strategic HotspotArea'}</h4>
                            <div class="grid grid-cols-2 gap-4 pt-2 border-t border-outline-variant/10">
                                <div>
                                    <p class="text-[8px] font-bold opacity-40 uppercase">Priority</p>
                                    <p class="text-xs font-black text-${h.priority > 7 ? 'error' : 'on-surface'}">${h.priority} / 10</p>
                                </div>
                                <div>
                                    <p class="text-[8px] font-bold opacity-40 uppercase">Reported</p>
                                    <p class="text-xs font-black text-on-surface">${h.created_at}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            },

            toggleViewMode() {
                if (this.map.getLayer('hotspots-heatmap')) {
                    this.map.setLayoutProperty('hotspots-heatmap', 'visibility', this.viewMode === 'heatmap' ? 'visible' : 'none');
                }
                this.renderMarkers();
            },

            setMapStyle(style) {
                this.mapStyle = style;
                this.map.setStyle('mapbox://styles/mapbox/' + style);
                // Wait for style to load before re-adding layers
                this.map.once('style.load', () => {
                    this.initMap(); // Re-init core layers
                });
            },

            toggleRecurrenceZones() {
                this.showRecurrence = !this.showRecurrence;
                this.map.setLayoutProperty('recurrence-zones', 'visibility', this.showRecurrence ? 'visible' : 'none');
            },

            resetFilters() {
                this.filters.status = ['pending', 'in_progress'];
                this.filters.categories = [];
            },

            flyTo(spot) {
                this.map.flyTo({ center: [spot.lng, spot.lat], zoom: 16, pitch: 60 });
                // Find and open popup
                const marker = this.markers.find(m => m.getLngLat().lng === spot.lng && m.getLngLat().lat === spot.lat);
                if (marker) marker.togglePopup();
            },

            centerOnCity() {
                this.map.flyTo({ center: [-0.1870, 5.6037], zoom: 12, pitch: 45 });
            }
        };
    }
</script>
@endsection
