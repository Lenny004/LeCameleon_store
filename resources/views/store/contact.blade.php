@extends('layouts.store')

@section('title', 'Contacto — Le Cameleon')

@section('content')
<div class="container content-page content-page--contact">
    <header class="content-page__header">
        <p class="content-page__eyebrow">Atención</p>
        <h1 class="content-page__title">Contacto</h1>
        <p class="content-page__intro">
            ¿Preguntas sobre una pieza, envíos o autenticidad? Escríbenos y te responderemos lo antes posible.
        </p>
    </header>

    @if (session('success'))
        <div class="flash flash--success content-page__alert" role="status">{{ session('success') }}</div>
    @endif

    <div class="content-page__panel">
        <form method="POST" action="{{ route('contact.store') }}" class="content-page__form">
            @csrf

            <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>

            <div class="form-group">
                <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input @error('name') form-input--error @enderror"
                    value="{{ old('name', auth()->user()?->name) }}"
                    placeholder="María López"
                    maxlength="255"
                    required
                    autocomplete="name"
                    @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                >
                @error('name')
                    <span class="form-error" id="name-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Correo electrónico <span class="form-label__required" aria-hidden="true">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input @error('email') form-input--error @enderror"
                    value="{{ old('email', auth()->user()?->email) }}"
                    placeholder="tu@correo.com"
                    maxlength="255"
                    inputmode="email"
                    required
                    autocomplete="email"
                    @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                >
                @error('email')
                    <span class="form-error" id="email-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="message">Mensaje <span class="form-label__required" aria-hidden="true">*</span></label>
                <textarea
                    id="message"
                    name="message"
                    class="form-textarea @error('message') form-textarea--error @enderror"
                    rows="6"
                    maxlength="5000"
                    placeholder="Cuéntanos en qué podemos ayudarte."
                    required
                    @error('message') aria-invalid="true" aria-describedby="message-error" @enderror
                >{{ old('message') }}</textarea>
                @error('message')
                    <span class="form-error" id="message-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn--primary">Enviar mensaje</button>
        </form>
    </div>
</div>
@endsection
