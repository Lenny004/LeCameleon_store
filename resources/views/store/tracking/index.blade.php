@extends('layouts.store')

@section('title', 'Rastrear envío — Le Cameleon')

@section('content')
{{-- Guest tracking lookup: enter LC-SV-XXXXXX code to view shipment timeline. --}}
<div class="container tracking">
    <p class="text-small text-muted tracking__eyebrow">Seguimiento</p>
    <h1 class="heading-2 tracking__title">Rastrear tu envío</h1>
    <p class="text-lead tracking__intro">
        Ingresa el código de rastreo que recibiste por correo o en tu pedido (formato <strong>LC-SV-XXXXXX</strong>).
    </p>

    <form method="POST" action="{{ route('tracking.lookup') }}" class="tracking-form">
        @csrf
        <div class="form-group tracking-form__field">
            <label class="form-label" for="tracking_code">Código de rastreo</label>
            <div class="tracking-form__row">
                <input
                    type="text"
                    id="tracking_code"
                    name="code"
                    class="form-input @error('code') form-input--error @enderror"
                    value="{{ old('code') }}"
                    placeholder="LC-SV-ABC123"
                    autocomplete="off"
                    autocapitalize="characters"
                    spellcheck="false"
                    required
                    aria-describedby="tracking_code_hint"
                >
                <button type="submit" class="btn btn--primary">Buscar</button>
            </div>
            @error('code')
                <p class="form-error" role="alert">{{ $message }}</p>
            @enderror
            <p id="tracking_code_hint" class="tracking-form__hint">
                También puedes consultar el estado en
                <a href="{{ route('account.orders.index') }}">Mis pedidos</a> si iniciaste sesión.
            </p>
        </div>
    </form>
</div>
@endsection
