@extends('layouts.store')

@section('title', 'Contacto — Le Cameleon')

@section('content')
<div class="container" style="padding-block:var(--space-2xl);max-width:32rem;">
    <h1 class="heading-2" style="margin-bottom:var(--space-sm);">Contacto</h1>
    <p class="text-muted" style="margin-bottom:var(--space-xl);">
        ¿Preguntas sobre una pieza, envíos o autenticidad? Escríbenos y te responderemos lo antes posible.
    </p>

    @if (session('success'))
        <div class="alert alert--success" style="margin-bottom:var(--space-lg);">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}" class="checkout-section">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">Nombre</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-input @error('name') form-input--error @enderror"
                value="{{ old('name', auth()->user()?->name) }}"
                required
                autocomplete="name"
            >
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Correo electrónico</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-input @error('email') form-input--error @enderror"
                value="{{ old('email', auth()->user()?->email) }}"
                required
                autocomplete="email"
            >
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="message">Mensaje</label>
            <textarea
                id="message"
                name="message"
                class="form-input @error('message') form-input--error @enderror"
                rows="6"
                required
            >{{ old('message') }}</textarea>
            @error('message')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn--primary">Enviar mensaje</button>
    </form>
</div>
@endsection
