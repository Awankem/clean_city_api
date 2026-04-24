@extends('layouts.admin')

@section('title', 'Administrative Audit Trail')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-on-surface font-heading tracking-tight">Audit Logs</h2>
            <p class="text-on-surface-variant font-medium">Transparency log of all administrative actions and system events.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-surface-container-high text-on-surface px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 border border-outline-variant/10">
                <span class="material-symbols-outlined text-sm">filter_alt</span>
                Filter Actions
            </button>
            <button class="bg-surface-container-high text-on-surface px-4 py-2 rounded-xl text-sm font-bold flex items-center gap-2 border border-outline-variant/10">
                <span class="material-symbols-outlined text-sm">download</span>
                CSV Export
            </button>
        </div>
    </div>

    <!-- Audit Timeline -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant/10 bg-surface-container-low/10">
            <h3 class="text-xl font-bold text-on-surface font-heading">System Activity Feed</h3>
        </div>

        <div class="p-8">
            <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-outline-variant/20 before:to-transparent">
                
                @foreach($logs as $log)
                    <!-- Log Item -->
                    <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: {{ $loop->index * 50 }}ms">
                        <!-- Icon Dot -->
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border border-surface bg-on-surface text-surface shadow md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 transition-transform group-hover:scale-110 z-10 sticky top-0">
                            <span class="material-symbols-outlined text-sm">@if($log->new_status === 'resolved') check @elseif($log->new_status === 'in_progress') refresh @else pending @endif</span>
                        </div>
                        <!-- Content Card -->
                        <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-surface-container-low p-6 rounded-3xl border border-outline-variant/10 shadow-sm group-hover:bg-surface-container-lowest transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <time class="text-[10px] font-black text-primary uppercase tracking-widest">{{ $log->created_at->format('M d, Y - H:i') }}</time>
                                <span class="bg-primary/10 text-primary text-[10px] font-black px-2 py-0.5 rounded-full uppercase">Status Update</span>
                            </div>
                            <div class="text-sm font-bold text-on-surface mb-3">
                                <span class="text-on-surface-variant font-medium">Administrator</span> {{ $log->changedBy->name }} 
                                <span class="text-on-surface-variant font-medium">updated report</span> #{{ $log->report->id }} 
                                <span class="text-on-surface-variant font-medium">from</span> <span class="capitalize">{{ $log->old_status }}</span>
                                <span class="text-on-surface-variant font-medium">to</span> <span class="capitalize text-primary">{{ $log->new_status }}</span>
                            </div>
                            
                            @if($log->note)
                                <div class="bg-surface-container p-4 rounded-2xl border border-outline-variant/5">
                                    <p class="text-xs text-on-surface-variant italic font-medium leading-relaxed">
                                        "{{ $log->note }}"
                                    </p>
                                </div>
                            @endif

                            <div class="mt-4 pt-4 border-t border-outline-variant/5 flex items-center justify-between">
                                <a href="{{ route('admin.reports.show', $log->report->id) }}" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View Original Report →</a>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <div class="p-6 border-t border-outline-variant/10 bg-surface-container-low/10">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
