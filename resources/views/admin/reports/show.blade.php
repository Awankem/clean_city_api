@extends('layouts.admin')

@section('title', 'Report Detail - #CC-' . str_pad($report->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.reports.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-surface-container hover:bg-surface-container-high transition-colors text-on-surface-variant">
            <span class="material-symbols-outlined" data-icon="arrow_back">arrow_back</span>
        </a>
        <div>
            <h2 class="text-2xl font-black text-primary font-heading tracking-tight">Report #CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-sm text-on-surface-variant">Submitted by <strong>{{ $report->user->name ?? 'Anonymous' }}</strong> on {{ $report->created_at->format('M d, Y \a\t H:i') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Details & Images -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Report Core Info -->
            <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1">Issue Category</span>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary" data-icon="category">category</span>
                            <span class="text-lg font-bold text-on-surface">{{ $report->category->name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1">Priority Score</span>
                        <div class="flex items-center gap-3">
                            <div class="w-24 bg-surface-container-highest h-2 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $report->priority_score > 7 ? 'tertiary' : ($report->priority_score > 4 ? 'secondary' : 'primary') }}" style="width: {{ $report->priority_score * 10 }}%"></div>
                            </div>
                            <span class="text-xl font-black text-on-surface">{{ number_format($report->priority_score, 1) }}</span>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1">Description</span>
                    <p class="text-on-surface leading-relaxed">{{ $report->description ?? 'No description provided.' }}</p>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-1">Location Details</span>
                    <div class="flex items-start gap-2 text-on-surface-variant">
                        <span class="material-symbols-outlined text-sm mt-0.5" data-icon="location_on">location_on</span>
                        <p class="text-sm font-medium">Latitude: {{ $report->latitude }}, Longitude: {{ $report->longitude }}</p>
                    </div>
                    @if($report->address)
                        <p class="text-sm mt-1 ml-6">{{ $report->address }}</p>
                    @endif
                </div>
            </div>

            <!-- Image Gallery -->
            <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm">
                <h3 class="text-lg font-bold text-on-surface font-heading mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary" data-icon="photo_library">photo_library</span>
                    Evidence Records
                </h3>
                
                @if($report->images->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($report->images as $image)
                            <div class="group relative aspect-video rounded-xl overflow-hidden border border-outline-variant/10 bg-surface-container">
                                <img src="/storage/{{ $image->image_path }}" alt="Report Image" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a href="/storage/{{ $image->image_path }}" target="_blank" class="p-2 bg-white rounded-full text-on-surface">
                                        <span class="material-symbols-outlined" data-icon="zoom_in">zoom_in</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 border-2 border-dashed border-outline-variant/20 rounded-2xl flex flex-col items-center justify-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50" data-icon="image_not_supported">image_not_supported</span>
                        <p class="text-sm font-medium">No images submitted for this report.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column: Status & History -->
        <div class="space-y-8">
            <!-- Update Status Card -->
            <div class="bg-surface-container-lowest p-8 rounded-2xl border border-outline-variant/10 shadow-sm sticky top-24">
                <h3 class="text-lg font-bold text-on-surface font-heading mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary" data-icon="edit_note">edit_note</span>
                    Administrative Action
                </h3>

                <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="status" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-2">Update Status</label>
                        <select name="status" id="status" class="w-full bg-surface-container border-outline-variant/20 rounded-xl px-4 py-3 text-sm font-bold text-on-surface focus:ring-primary focus:border-primary">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>PENDING REVIEW</option>
                            <option value="in_progress" {{ $report->status == 'in_progress' ? 'selected' : '' }}>IN PROGRESS</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>RESOLVED</option>
                        </select>
                    </div>

                    <div>
                        <label for="note" class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest block mb-2">Internal Note (Optional)</label>
                        <textarea name="note" id="note" rows="3" class="w-full bg-surface-container border-outline-variant/20 rounded-xl px-4 py-3 text-sm text-on-surface focus:ring-primary focus:border-primary placeholder:text-on-surface-variant/50" placeholder="Describe actions taken..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-primary text-on-primary py-4 rounded-xl text-sm font-black tracking-tight shadow-sm hover:opacity-90 active:scale-95 transition-all">
                        SUBMIT UPDATE
                    </button>
                </form>

                <div class="mt-8 pt-8 border-t border-outline-variant/10">
                    <h4 class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-4">Status Timeline</h4>
                    <div class="space-y-6">
                        @forelse($report->statusHistory as $history)
                            <div class="relative pl-6 border-l-2 border-primary/20">
                                <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-primary/20 border-2 border-primary"></div>
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-[10px] font-black text-primary uppercase">{{ str_replace('_', ' ', $history->new_status) }}</span>
                                    <span class="text-[9px] text-on-surface-variant">{{ $history->created_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-xs text-on-surface leading-tight">{{ $history->note ?? 'Status changed by Admin' }}</p>
                                <p class="text-[9px] font-bold text-on-surface-variant mt-1 uppercase">Admin: {{ $history->changedBy->name ?? 'System' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-on-surface-variant italic">No status changes recorded yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
