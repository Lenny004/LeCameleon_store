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
</div>
@endsection
