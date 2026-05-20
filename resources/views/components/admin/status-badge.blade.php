@props(['status'])

@php
    $styles = [
        'pending' => 'bg-tertiary-container text-on-tertiary',
        'in_progress' => 'bg-secondary-container text-on-secondary-container',
        'resolved' => 'bg-primary-container text-on-primary',
    ];
    $class = $styles[$status] ?? 'bg-surface-container-high text-on-surface-variant';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {$class}"]) }}>
    {{ str_replace('_', ' ', $status) }}
</span>
