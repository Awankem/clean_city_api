@extends('layouts.admin')

@section('title', 'Report Management')
@section('breadcrumb', 'Reports')

@section('content')
<div class="space-y-6">
    <x-admin.page-header
        title="Report Management"
        description="Review and manage citizen-submitted waste issues."
    >
        <x-slot:actions>
            <a href="{{ route('admin.reports.export', request()->only(['search', 'status'])) }}" class="admin-btn-secondary py-2.5">
                <span class="material-symbols-outlined text-lg">download</span>
                Export CSV
            </a>
            <span class="inline-flex items-center gap-2 text-sm font-bold bg-surface-container-lowest px-4 py-2.5 rounded-xl border border-outline-variant/15">
                <span class="text-on-surface-variant text-xs uppercase">Total</span>
                <span class="text-primary text-lg">{{ $reports->total() }}</span>
            </span>
        </x-slot:actions>
    </x-admin.page-header>

    @if($search)
        <p class="text-sm text-on-surface-variant">
            Showing results for <strong class="text-on-surface">"{{ $search }}"</strong>
            — <a href="{{ route('admin.reports.index', request()->only('status')) }}" class="text-primary font-bold hover:underline">Clear search</a>
        </p>
    @endif

    <div class="admin-card p-4 flex flex-wrap gap-3 items-center">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-on-surface-variant">
            <span class="material-symbols-outlined text-base">filter_list</span>
            Status
        </span>
        <div class="flex flex-wrap gap-2">
            @php
                $filters = [
                    'all' => 'All',
                    'pending' => 'Pending',
                    'in_progress' => 'In review',
                    'resolved' => 'Resolved',
                ];
            @endphp
            @foreach($filters as $value => $label)
                <a href="{{ route('admin.reports.index', array_filter(['status' => $value === 'all' ? null : $value, 'search' => $search ?: null])) }}"
                   class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider transition-colors {{ ($status ?? 'all') === $value ? 'bg-primary text-on-primary' : 'bg-surface-container-high text-on-surface-variant hover:bg-surface-container-highest' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <x-admin.card :padding="false">
        <div class="overflow-x-auto">
            <table class="admin-table w-full text-left">
                <thead class="bg-surface-container-low/80">
                    <tr>
                        <th>Report ID</th>
                        <th>Submitted</th>
                        <th>Citizen</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($reports as $report)
                    <tr>
                        <td class="font-mono text-primary font-bold">#CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-xs text-on-surface-variant">{{ $report->created_at->format('M d, H:i') }}</td>
                        <td class="font-medium">{{ $report->user->name ?? 'Anonymous' }}</td>
                        <td class="text-on-surface-variant">{{ $report->category->name ?? 'Uncategorized' }}</td>
                        <td><x-admin.priority-bar :score="$report->priority_score" /></td>
                        <td><x-admin.status-badge :status="$report->status" /></td>
                        <td class="text-right">
                            <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline">
                                Details <span class="material-symbols-outlined text-sm">open_in_new</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-on-surface-variant">No reports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-outline-variant/10 bg-surface-container-low/40 pagination">
            {{ $reports->links() }}
        </div>
    </x-admin.card>
</div>
@endsection
