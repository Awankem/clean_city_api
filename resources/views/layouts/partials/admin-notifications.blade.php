<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <button type="button"
            class="relative w-10 h-10 flex items-center justify-center rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors"
            @click="open = !open"
            aria-label="Notifications"
            :aria-expanded="open">
        <span class="material-symbols-outlined">notifications</span>
        @if(($unreadNotificationCount ?? 0) > 0)
            <span class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-tertiary text-on-tertiary text-[10px] font-black leading-none">
                {{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition
         class="absolute right-0 mt-2 w-[min(100vw-2rem,22rem)] bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-xl z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-outline-variant/10">
            <h3 class="text-sm font-bold font-heading">Notifications</h3>
            @if(($unreadNotificationCount ?? 0) > 0)
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] font-bold text-primary uppercase tracking-wider hover:underline">
                        Mark all read
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto custom-scrollbar divide-y divide-outline-variant/10">
            @forelse($adminNotifications ?? [] as $notification)
                <a href="{{ $notification->report_id ? route('admin.reports.show', $notification->report_id) : '#' }}"
                   class="block px-4 py-3 hover:bg-surface-container-low transition-colors {{ $notification->read_at ? 'opacity-70' : 'bg-primary/5' }}"
                   @if(!$notification->read_at)
                   onclick="fetch('{{ route('admin.notifications.read', $notification) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } })"
                   @endif>
                    <div class="flex gap-2">
                        @if(!$notification->read_at)
                            <span class="w-2 h-2 rounded-full bg-primary shrink-0 mt-1.5"></span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-on-surface truncate">{{ $notification->title }}</p>
                            <p class="text-[11px] text-on-surface-variant mt-0.5 line-clamp-2">{{ $notification->message }}</p>
                            <p class="text-[10px] text-on-surface-variant/70 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </a>
            @empty
                <p class="px-4 py-8 text-center text-sm text-on-surface-variant">No notifications yet.</p>
            @endforelse
        </div>
    </div>
</div>
