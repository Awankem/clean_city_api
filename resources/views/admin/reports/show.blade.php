@extends('layouts.admin')

@section('title', 'Report #CC-' . str_pad($report->id, 4, '0', STR_PAD_LEFT))
@section('breadcrumb', 'Report detail')

@section('content')
<div class="space-y-6">
    <div class="flex items-start gap-4">
        <a href="{{ route('admin.reports.index') }}" class="w-11 h-11 flex items-center justify-center rounded-xl bg-surface-container-lowest border border-outline-variant/15 hover:bg-surface-container transition-colors shrink-0" aria-label="Back to reports">
            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
        </a>
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-black text-primary font-heading tracking-tight">Report #CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</h2>
            <p class="text-sm text-on-surface-variant mt-1">
                Submitted by <strong class="text-on-surface">{{ $report->user->name ?? 'Anonymous' }}</strong>
                · {{ $report->created_at->format('M d, Y \a\t H:i') }}
            </p>
        </div>
        <x-admin.status-badge :status="$report->status" class="shrink-0" />
        
        <a href="{{ route('admin.chat.show', $report->id) }}" class="shrink-0 admin-btn-secondary flex items-center gap-2">
            <span class="material-symbols-outlined">chat</span>
            <span>Chat</span>
            @if($report->unread_messages_count > 0)
                <span class="bg-error text-on-error text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                    {{ $report->unread_messages_count }}
                </span>
            @endif
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-admin.card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 -mt-2">
                    <div>
                        <span class="admin-label">Issue category</span>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="material-symbols-outlined text-primary">category</span>
                            <span class="text-lg font-bold">{{ $report->category->name ?? 'Uncategorized' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="admin-label">Priority score</span>
                        <div class="mt-2"><x-admin.priority-bar :score="$report->priority_score" width="w-28" /></div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-outline-variant/10">
                    <span class="admin-label">Description</span>
                    <p class="mt-2 text-on-surface leading-relaxed">{{ $report->description ?? 'No description provided.' }}</p>
                </div>

                <div class="mt-8 pt-8 border-t border-outline-variant/10">
                    <span class="admin-label">Location</span>
                    <div class="flex items-start gap-2 mt-2 text-sm text-on-surface-variant">
                        <span class="material-symbols-outlined text-base shrink-0">location_on</span>
                        <p>{{ $report->latitude }}, {{ $report->longitude }}@if($report->address) — {{ $report->address }}@endif</p>
                    </div>
                    <div class="mt-4 rounded-2xl overflow-hidden border border-outline-variant/15 aspect-video">
                        <img src="https://api.mapbox.com/styles/v1/mapbox/dark-v11/static/pin-s+ff0000({{ $report->longitude }},{{ $report->latitude }})/{{ $report->longitude }},{{ $report->latitude }},15,0/600x300?access_token={{ config('services.mapbox.access_token') }}"
                             alt="Report location" class="w-full h-full object-cover">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Evidence photos">
                @if($report->images->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 -mt-2">
                        @foreach($report->images as $image)
                            <a href="{{ $image->image_url }}" target="_blank" rel="noopener"
                               class="group relative aspect-video rounded-xl overflow-hidden border border-outline-variant/15 bg-surface-container block">
                                <img src="{{ $image->image_url }}" alt="" class="w-full h-full object-cover transition-transform group-hover:scale-105">
                                <div class="absolute inset-0 bg-on-surface/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="material-symbols-outlined text-on-primary text-3xl">zoom_in</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-on-surface-variant border-2 border-dashed border-outline-variant/20 rounded-2xl">
                        <span class="material-symbols-outlined text-4xl opacity-40 block mb-2">image_not_supported</span>
                        <p class="text-sm font-medium">No images submitted.</p>
                    </div>
                @endif
            </x-admin.card>
        </div>

        <div class="space-y-6">
            <x-admin.card title="Update status" class="lg:sticky lg:top-24">
                @if($errors->has('status'))
                    <div class="mb-4 p-3 rounded-xl bg-tertiary/10 border border-tertiary/20 text-tertiary text-sm font-semibold flex gap-2 -mt-2">
                        <span class="material-symbols-outlined text-lg shrink-0">info</span>
                        {{ $errors->first('status') }}
                    </div>
                @endif

                @if(count($allowedNextStatuses) === 0)
                    <div class="space-y-3 -mt-2">
                        <p class="text-sm text-on-surface-variant">
                            This report is <strong class="text-on-surface">{{ $currentStatus?->label() ?? ucfirst($report->status) }}</strong>.
                            Final statuses cannot be changed.
                        </p>
                        <div class="admin-input mt-0 font-semibold bg-surface-container-low cursor-not-allowed opacity-80">
                            {{ $currentStatus?->label() ?? $report->status }}
                        </div>
                    </div>
                @else
                <form action="{{ route('admin.reports.update-status', $report->id) }}" method="POST" class="space-y-5 -mt-2">
                    @csrf
                    <div>
                        <label for="status" class="admin-label">Next status</label>
                        <p class="text-xs text-on-surface-variant mb-2">
                            Current: <strong>{{ $currentStatus?->label() }}</strong>
                            — only forward moves are allowed.
                        </p>
                        <select name="status" id="status" class="admin-input mt-1 font-semibold" required>
                            @foreach($allowedNextStatuses as $nextStatus)
                                <option value="{{ $nextStatus->value }}">{{ $nextStatus->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="note" class="admin-label">Internal note (optional)</label>
                        <textarea name="note" id="note" rows="3" class="admin-input mt-1.5 resize-none"
                                  placeholder="Describe actions taken…"></textarea>
                    </div>
                    <button type="submit" class="admin-btn-primary w-full py-3.5">Save & notify citizen</button>
                </form>
                @endif

                <div class="mt-8 pt-6 border-t border-outline-variant/10">
                    <h4 class="admin-label mb-4">Status timeline</h4>
                    <div class="space-y-5">
                        @forelse($report->statusHistory as $history)
                            <div class="relative pl-5 border-l-2 border-primary/25">
                                <div class="absolute -left-[7px] top-1 w-3 h-3 rounded-full bg-primary ring-4 ring-primary/15"></div>
                                <div class="flex justify-between gap-2 mb-1">
                                    <span class="text-[10px] font-black text-primary uppercase">{{ str_replace('_', ' ', $history->new_status) }}</span>
                                    <span class="text-[10px] text-on-surface-variant">{{ $history->created_at->format('M d, H:i') }}</span>
                                </div>
                                <p class="text-xs text-on-surface">{{ $history->note ?? 'Status changed' }}</p>
                                <p class="text-[10px] text-on-surface-variant mt-1">{{ $history->changedBy->name ?? 'System' }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-on-surface-variant italic">No status changes yet.</p>
                        @endforelse
                    </div>
                </div>
            </x-admin.card>
        </div>
    </div>
</div>
@endsection
