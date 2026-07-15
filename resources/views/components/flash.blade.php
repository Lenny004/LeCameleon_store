@if (session('success') || session('error') || session('warning') || session('info') || ($errors ?? null)?->any())
    <div class="container" style="padding-top: var(--space-md);">
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
            @if (($errors ?? null)?->any())
                <div class="flash flash--error" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endif
