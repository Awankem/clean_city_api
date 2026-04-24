@extends('layouts.admin')

@section('title', 'All Reports')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-black text-primary font-heading tracking-tight">Report Management</h2>
            <p class="text-sm text-on-surface-variant">Review and manage citizen-submitted waste issues</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-surface-container-lowest px-4 py-2 rounded-lg border border-outline-variant/10 shadow-sm flex items-center gap-2">
                <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Total:</span>
                <span class="text-sm font-black text-primary">{{ $reports->total() }}</span>
            </div>
        </div>
    </div>

    <!-- Filters placeholder -->
    <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant/10 shadow-sm flex flex-wrap gap-4 items-center">
        <div class="flex items-center gap-2 px-3 py-1.5 bg-surface-container rounded-lg border border-outline-variant/5">
            <span class="material-symbols-outlined text-sm text-on-surface-variant" data-icon="filter_list">filter_list</span>
            <span class="text-xs font-bold text-on-surface">Filter by Status</span>
        </div>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-primary text-on-primary rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer">All</span>
            <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer hover:bg-surface-container-highest transition-colors">Pending</span>
            <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer hover:bg-surface-container-highest transition-colors">In Progress</span>
            <span class="px-3 py-1 bg-surface-container-high text-on-surface-variant rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer hover:bg-surface-container-highest transition-colors">Resolved</span>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm overflow-hidden border border-outline-variant/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-surface-container-low/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Report ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Citizen</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Category</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @foreach($reports as $report)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-mono text-primary font-bold">#CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 text-xs text-on-surface-variant">{{ $report->created_at->format('M d, H:i') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">{{ $report->user->name ?? 'Anonymous' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant">{{ $report->category->name ?? 'Uncategorized' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-16 bg-surface-container-highest h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full bg-{{ $report->priority_score > 7 ? 'tertiary' : ($report->priority_score > 4 ? 'secondary' : 'primary') }}" style="width: {{ $report->priority_score * 10 }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-on-surface">{{ number_format($report->priority_score, 1) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-tertiary-container text-white',
                                    'in_progress' => 'bg-secondary-container text-on-secondary',
                                    'resolved' => 'bg-primary-container text-on-primary',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusColors[$report->status] ?? 'bg-surface-container-high' }}">
                                {{ str_replace('_', ' ', $report->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex items-center gap-1.5 text-primary hover:underline text-xs font-bold">
                                <span>Details</span>
                                <span class="material-symbols-outlined text-sm" data-icon="open_in_new">open_in_new</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 bg-surface-container-low/30 border-t border-outline-variant/10">
            {{ $reports->links() }}
        </div>
    </div>
</div>
@endsection
