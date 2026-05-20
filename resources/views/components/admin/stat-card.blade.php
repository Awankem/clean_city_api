@props([
    'label',
    'value',
    'icon',
    'accent' => 'primary',
    'badge' => null,
])

@php
    $accents = [
        'primary' => ['border' => 'border-primary/30', 'iconBg' => 'bg-primary/10', 'iconText' => 'text-primary', 'badge' => 'text-primary bg-primary/10'],
        'secondary' => ['border' => 'border-secondary-container/50', 'iconBg' => 'bg-secondary-container/20', 'iconText' => 'text-secondary', 'badge' => 'text-secondary bg-secondary-container/30'],
        'tertiary' => ['border' => 'border-tertiary/30', 'iconBg' => 'bg-tertiary/10', 'iconText' => 'text-tertiary', 'badge' => 'text-tertiary bg-tertiary/10'],
        'success' => ['border' => 'border-primary-container/30', 'iconBg' => 'bg-primary-container/10', 'iconText' => 'text-primary-container', 'badge' => 'text-primary-container bg-primary-container/10'],
    ];
    $a = $accents[$accent] ?? $accents['primary'];
@endphp

<div {{ $attributes->merge(['class' => "admin-stat-card border-l-4 {$a['border']}"]) }}>
    <div class="flex justify-between items-start mb-4">
        <span class="p-3 rounded-xl {{ $a['iconBg'] }}">
            <span class="material-symbols-outlined {{ $a['iconText'] }}">{{ $icon }}</span>
        </span>
        @if($badge)
            <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $a['badge'] }}">{{ $badge }}</span>
        @endif
    </div>
    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wider mb-1">{{ $label }}</p>
    <h3 class="text-3xl font-black text-on-surface tracking-tight">{{ $value }}</h3>
</div>
