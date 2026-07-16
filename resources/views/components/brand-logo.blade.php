@props([
    'variant' => 'auto', // auto | compact | horizontal | wide (+ aliases: icon, lockup, full)
    'href' => null,
    'size' => 'md', // sm | md | lg | xl
    'context' => null, // null | header | guest | hero — ajusta tamaño y almohadilla por zona
    'inverted' => false,
])

@php
    $variantAliases = [
        'icon' => 'compact',
        'lockup' => 'auto',
        'full' => 'horizontal',
        'wordmark' => 'horizontal',
    ];
    $variant = $variantAliases[$variant] ?? $variant;
    if (! in_array($variant, ['auto', 'compact', 'horizontal', 'wide'], true)) {
        $variant = 'auto';
    }

    $compact = config('store.brand_logo_icon');      // logo_icon.png — stacked / narrower
    $horizontal = config('store.brand_logo');        // logo.png — standard wide
    $wide = config('store.brand_logo_wide');         // logo3.png — widest lockup

    $has = static fn (?string $path): bool => is_string($path) && $path !== '' && file_exists(public_path($path));

    $hasCompact = $has($compact);
    $hasHorizontal = $has($horizontal);
    $hasWide = $has($wide);

    $tag = $href ? 'a' : 'span';
    $contextClass = in_array($context, ['header', 'guest', 'hero'], true)
        ? 'brand-logo--ctx-'.$context
        : null;

    $classes = trim(implode(' ', array_filter([
        'brand-logo',
        'brand-logo--'.$variant,
        'brand-logo--'.$size,
        $contextClass,
        $inverted ? 'brand-logo--inverted' : null,
        $attributes->get('class'),
    ])));
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->except('class')->merge([
        'class' => $classes,
        'aria-label' => $href ? 'Le Cameleon — Inicio' : null,
    ]) }}
>
    @if ($variant === 'auto')
        @if ($hasCompact)
            <img
                src="{{ asset($compact) }}"
                alt="Le Caméléon Vintage Shop"
                class="brand-logo__img brand-logo__img--compact"
                decoding="async"
                width="120"
                height="90"
            >
        @endif
        @if ($hasHorizontal)
            <img
                src="{{ asset($horizontal) }}"
                alt=""
                class="brand-logo__img brand-logo__img--horizontal"
                decoding="async"
                width="220"
                height="58"
                aria-hidden="true"
            >
        @endif
        @if ($hasWide)
            <img
                src="{{ asset($wide) }}"
                alt=""
                class="brand-logo__img brand-logo__img--wide"
                decoding="async"
                width="280"
                height="72"
                aria-hidden="true"
            >
        @endif
        @unless ($hasCompact || $hasHorizontal || $hasWide)
            <span class="brand-logo__fallback">Le <span>Cameleon</span></span>
        @endunless
    @elseif ($variant === 'compact' && $hasCompact)
        <img
            src="{{ asset($compact) }}"
            alt="Le Caméléon Vintage Shop"
            class="brand-logo__img brand-logo__img--compact"
            decoding="async"
            width="120"
            height="90"
        >
    @elseif ($variant === 'wide' && ($hasWide || $hasHorizontal))
        <img
            src="{{ asset($hasWide ? $wide : $horizontal) }}"
            alt="Le Caméléon Vintage Shop"
            class="brand-logo__img brand-logo__img--wide"
            decoding="async"
            width="280"
            height="72"
        >
    @elseif ($hasHorizontal || $hasWide || $hasCompact)
        <img
            src="{{ asset($hasHorizontal ? $horizontal : ($hasWide ? $wide : $compact)) }}"
            alt="Le Caméléon Vintage Shop"
            class="brand-logo__img brand-logo__img--horizontal"
            decoding="async"
            width="220"
            height="58"
        >
    @else
        <span class="brand-logo__fallback">Le <span>Cameleon</span></span>
    @endif
</{{ $tag }}>
