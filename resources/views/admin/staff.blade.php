@extends('layouts.admin')

@section('title', 'Staff & Team Management')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-on-surface font-heading tracking-tight">Staff & Teams</h2>
            <p class="text-on-surface-variant font-medium">Manage municipal officers, collection teams, and system administrators.</p>
        </div>
        <button class="bg-primary text-on-primary px-6 py-3 rounded-2xl text-sm font-black shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">person_add</span>
            Invite Team Member
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                <span class="material-symbols-outlined">badge</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest leading-none mb-1">Total Personnel</p>
                <h3 class="text-2xl font-black text-on-surface">{{ $stats['total_staff'] }}</h3>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-secondary/10 flex items-center justify-center text-secondary">
                <span class="material-symbols-outlined">admin_panel_settings</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest leading-none mb-1">Administrators</p>
                <h3 class="text-2xl font-black text-on-surface">{{ $stats['admins'] }}</h3>
            </div>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-3xl border border-outline-variant/10 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-tertiary/10 flex items-center justify-center text-tertiary">
                <span class="material-symbols-outlined">groups</span>
            </div>
            <div>
                <p class="text-xs font-bold text-on-surface-variant uppercase tracking-widest leading-none mb-1">Active Teams</p>
                <h3 class="text-2xl font-black text-on-surface">{{ $stats['teams_active'] }}</h3>
            </div>
        </div>
    </div>

    <!-- Staff Table Card -->
    <div class="bg-surface-container-lowest rounded-3xl border border-outline-variant/10 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant/10 flex items-center justify-between bg-surface-container-lowest/50">
            <h3 class="text-xl font-bold text-on-surface font-heading">Personnel Directory</h3>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                    <input type="text" placeholder="Filter staff..." class="pl-10 pr-4 py-2 bg-surface-container border border-outline-variant/10 rounded-xl text-xs focus:ring-1 focus:ring-primary outline-none">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low/30">
                        <th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest border-b border-outline-variant/10">Member</th>
                        <th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest border-b border-outline-variant/10">Official Role</th>
                        <th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest border-b border-outline-variant/10">Access Status</th>
                        <th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest border-b border-outline-variant/10">Joined</th>
                        <th class="px-6 py-4 text-[10px] font-black text-on-surface-variant uppercase tracking-widest border-b border-outline-variant/10 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @foreach($staff as $member)
                        <tr class="hover:bg-surface-container/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full border-2 border-primary/10 overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=f0f9f6&color=00482f" alt="{{ $member->name }}">
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-on-surface">{{ $member->name }}</p>
                                        <p class="text-[10px] font-medium text-on-surface-variant leading-tight">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full @if($member->role === 'admin') bg-primary @elseif($member->role === 'collector') bg-secondary @else bg-on-surface-variant @endif"></span>
                                    <span class="text-xs font-bold text-on-surface uppercase tracking-tight">{{ $member->role }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="bg-primary/10 text-primary text-[10px] font-black px-2 py-1 rounded-full uppercase">Authorized</span>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-xs font-medium text-on-surface-variant">{{ $member->created_at->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="p-2 text-on-surface-variant hover:text-primary transition-colors" title="Edit Permissions">
                                        <span class="material-symbols-outlined text-sm">edit</span>
                                    </button>
                                    <button class="p-2 text-on-surface-variant hover:text-error transition-colors" title="Revoke Access">
                                        <span class="material-symbols-outlined text-sm">no_accounts</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t border-outline-variant/10 bg-surface-container-low/10">
            {{ $staff->links() }}
        </div>
    </div>
</div>
@endsection
