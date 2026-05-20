@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4']) }}>
    <div>
        <h2 class="text-2xl md:text-3xl font-black text-primary font-heading tracking-tight">{{ $title }}</h2>
        @if($description)
            <p class="text-sm text-on-surface-variant mt-1 max-w-2xl">{{ $description }}</p>
        @endif
    </div>
    @if(isset($actions))
        <div class="flex flex-wrap items-center gap-3 shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
