@extends('layouts.admin')

@section('title', 'Chat: Report #CC-' . str_pad($report->id, 4, '0', STR_PAD_LEFT))
@section('breadcrumb', 'Report Chat')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-start gap-4">
        <a href="{{ route('admin.reports.show', $report->id) }}" class="w-11 h-11 flex items-center justify-center rounded-xl bg-surface-container-lowest border border-outline-variant/15 hover:bg-surface-container transition-colors shrink-0" aria-label="Back to report">
            <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
        </a>
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-black text-primary font-heading tracking-tight">Chat with Citizen</h2>
            <p class="text-sm text-on-surface-variant mt-1">
                Report #CC-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }} — 
                <strong>{{ $report->user->name ?? 'Anonymous' }}</strong>
            </p>
        </div>
    </div>

    <x-admin.card class="flex flex-col h-[600px] p-0 overflow-hidden">
        <div class="flex-1 overflow-y-auto p-6 space-y-6" id="chat-messages">
            @forelse($messages as $msg)
                @php
                    $isAdmin = $msg->sender->role === 'admin';
                @endphp
                <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[75%] rounded-2xl p-4 {{ $isAdmin ? 'bg-primary text-on-primary rounded-br-none' : 'bg-surface-container-high text-on-surface rounded-bl-none' }}">
                        @if(!$isAdmin)
                            <div class="text-xs font-bold text-on-surface-variant mb-1">{{ $msg->sender->name }}</div>
                        @endif
                        <p class="whitespace-pre-wrap text-sm">{{ $msg->message }}</p>
                        <div class="text-[10px] mt-2 text-right {{ $isAdmin ? 'text-on-primary/70' : 'text-on-surface-variant' }}">
                            {{ $msg->created_at->format('M d, H:i') }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl opacity-40 block mb-2">chat</span>
                    <p class="text-sm font-medium">No messages yet. Start the conversation!</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 bg-surface-container-lowest border-t border-outline-variant/15">
            <form action="{{ route('admin.chat.store', $report->id) }}" method="POST" class="flex gap-3">
                @csrf
                <textarea name="message" rows="2" class="admin-input flex-1 resize-none" placeholder="Type a message..." required></textarea>
                <button type="submit" class="admin-btn-primary self-end flex items-center gap-2">
                    <span>Send</span>
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </form>
        </div>
    </x-admin.card>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatContainer = document.getElementById('chat-messages');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Mark as read when page loads
        fetch('{{ route("admin.chat.read", $report->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    });
</script>
@endsection
