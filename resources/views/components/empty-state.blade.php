@props([
    'title' => 'Nada por aquí',
    'text' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div class="empty-state">
    <svg class="empty-state__icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7m16 0v5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-5m16 0h-2.586a1 1 0 0 0-.707.293l-2.414 2.414a1 1 0 0 1-.707.293h-3.172a1 1 0 0 1-.707-.293l-2.414-2.414A1 1 0 0 0 6.586 13H4"/>
    </svg>
    <h2 class="empty-state__title">{{ $title }}</h2>
    @if ($text)
        <p class="empty-state__text">{{ $text }}</p>
    @endif
    @if (!empty($actionLabel) && !empty($actionUrl))
        <div class="empty-state__actions">
            <a href="{{ $actionUrl }}" class="btn btn--primary">{{ $actionLabel }}</a>
        </div>
    @endif
</div>
