@props(['score', 'width' => 'w-16'])

@php
    $pct = min(100, max(0, (float) $score * 10));
    $color = $score > 7 ? '#ae001b' : ($score > 4 ? '#fec733' : '#00482f');
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    <div class="{{ $width }} bg-surface-container-highest h-1.5 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background-color: {{ $color }}"></div>
    </div>
    <span class="text-xs font-bold text-on-surface tabular-nums">{{ number_format((float) $score, 1) }}</span>
</div>
