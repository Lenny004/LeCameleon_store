@php
    $globalErrorKeys = ['cart', 'payment', 'checkout', 'stock', 'offer', 'review'];
    $globalErrors = collect(($errors ?? null)?->getMessages() ?? [])
        ->only($globalErrorKeys)
        ->flatten()
        ->values();
    $hasFieldErrors = collect(($errors ?? null)?->getMessages() ?? [])
        ->except($globalErrorKeys)
        ->isNotEmpty();
@endphp

@if (session('success') || session('error') || session('warning') || session('info') || ($errors ?? null)?->any())
    <div class="container flash-container">
        <div class="flash-stack">
            @if (session('success'))
                <div class="flash flash--success" role="alert">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="flash flash--error" role="alert">{{ session('error') }}</div>
            @endif
            @if (session('warning'))
                <div class="flash flash--warning" role="alert">{{ session('warning') }}</div>
            @endif
            @if (session('info'))
                <div class="flash flash--info" role="alert">{{ session('info') }}</div>
            @endif
            @if ($hasFieldErrors)
                <div class="flash flash--error" role="alert">Revisa los campos marcados.</div>
            @endif
            @if ($globalErrors->isNotEmpty())
                <div class="flash flash--error" role="alert">
                    <ul class="flash__list">
                        @foreach ($globalErrors as $error)
                            <li class="flash__list-item">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif
