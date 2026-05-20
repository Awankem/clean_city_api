@props(['title' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'admin-card overflow-hidden']) }}>
    @if($title || isset($header))
        <div class="admin-card-header">
            @if($title)
                <h3 class="text-lg font-bold text-on-surface font-heading">{{ $title }}</h3>
            @endif
            @isset($header)
                {{ $header }}
            @endisset
        </div>
    @endif
    <div @class(['p-6' => $padding])>
        {{ $slot }}
    </div>
</div>
