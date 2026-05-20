@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('breadcrumb', 'Compliance')

@section('content')
<div class="space-y-6">
    <x-admin.page-header
        title="Audit Logs"
        description="Transparency log of administrative actions and status changes."
    >
        <x-slot:actions>
            <button type="button" class="admin-btn-secondary py-2.5 opacity-70 cursor-default" title="Coming soon">
                <span class="material-symbols-outlined text-lg">filter_alt</span>
                Filter
            </button>
            <button type="button" class="admin-btn-secondary py-2.5 opacity-70 cursor-default" title="Coming soon">
                <span class="material-symbols-outlined text-lg">download</span>
                Export CSV
            </button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.card title="Activity feed" :padding="false">
        <div class="p-6 md:p-8 space-y-6">
            @forelse($logs as $log)
                <article class="flex gap-4 md:gap-6 group">
                    <div class="shrink-0 w-10 h-10 rounded-xl bg-primary text-on-primary flex items-center justify-center shadow-md shadow-primary/15">
                        <span class="material-symbols-outlined text-lg">
                            @if($log->new_status === 'resolved') check @elseif($log->new_status === 'in_progress') sync @else schedule @endif
                        </span>
                    </div>
                    <div class="flex-1 admin-card p-5 group-hover:shadow-md transition-shadow">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <time class="text-[10px] font-black text-primary uppercase tracking-widest">{{ $log->created_at->format('M d, Y · H:i') }}</time>
                            <span class="text-[10px] font-bold bg-primary/10 text-primary px-2.5 py-1 rounded-full uppercase">Status update</span>
                        </div>
                        <p class="text-sm text-on-surface leading-relaxed">
                            <strong>{{ $log->changedBy->name }}</strong>
                            updated report <strong>#{{ $log->report->id }}</strong>
                            from <span class="capitalize text-on-surface-variant">{{ $log->old_status }}</span>
                            to <span class="capitalize text-primary font-bold">{{ $log->new_status }}</span>
                        </p>
                        @if($log->note)
                            <blockquote class="mt-3 text-xs text-on-surface-variant italic bg-surface-container rounded-xl px-4 py-3 border border-outline-variant/10">
                                “{{ $log->note }}”
                            </blockquote>
                        @endif
                        <a href="{{ route('admin.reports.show', $log->report->id) }}" class="inline-flex items-center gap-1 mt-4 text-[10px] font-bold text-primary uppercase tracking-widest hover:underline">
                            View report <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                </article>
            @empty
                <p class="text-center py-12 text-on-surface-variant">No audit entries yet.</p>
            @endforelse
        </div>
        <div class="px-6 py-4 border-t border-outline-variant/10 bg-surface-container-low/40 pagination">
            {{ $logs->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
