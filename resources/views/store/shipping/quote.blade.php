@extends('layouts.store')

@section('title', 'Calculadora de envío — Le Cameleon')

@section('content')
{{-- Public shipping quote calculator: municipality → fee, ETA, next dispatch, warnings. --}}
<div class="container shipping-quote">
    <p class="text-small text-muted shipping-quote__eyebrow">Logística</p>
    <h1 class="heading-2 shipping-quote__title">Calculadora de envío</h1>
    <p class="text-lead shipping-quote__intro">
        Consulta el costo estimado y los tiempos de entrega según tu municipio en El Salvador.
    </p>

    <div
        class="shipping-quote__panel"
        x-data="shippingQuote({
            calculateUrl: @js(route('shipping.quote.calculate')),
            flatRate: {{ (float) ($flatRate ?? 0) }},
        })"
    >
        <div class="form-group">
            <label class="form-label" for="quote_municipality">Municipio de destino</label>
            <select
                id="quote_municipality"
                class="form-input"
                x-model="municipalityId"
                @change="fetchQuote()"
            >
                <option value="">Selecciona un municipio…</option>
                @foreach ($departments as $department)
                    <optgroup label="{{ $department->name }}">
                        @foreach ($department->municipalities as $municipality)
                            <option value="{{ $municipality->id }}">{{ $municipality->name }}</option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="shipping-quote__result" x-show="municipalityId" x-cloak>
            <template x-if="loading">
                <p class="shipping-quote__status" aria-live="polite">Calculando envío…</p>
            </template>

            <template x-if="error && !loading">
                <div class="flash flash--error" role="alert">
                    <p x-text="error" style="margin:0;"></p>
                </div>
            </template>

            <template x-if="quote && !loading">
                <div class="shipping-quote__details">
                    <div class="shipping-quote__fee">
                        <span class="shipping-quote__fee-label">Costo de envío</span>
                        <span class="shipping-quote__fee-value" x-text="formatMoney(quote.fee)"></span>
                    </div>

                    <dl class="shipping-quote__meta">
                        <div class="shipping-quote__meta-row">
                            <dt>Entrega estimada</dt>
                            <dd x-text="formatEta(quote.eta_hours)"></dd>
                        </div>
                        <div class="shipping-quote__meta-row">
                            <dt>Próximo despacho</dt>
                            <dd x-text="formatDispatch(quote.next_dispatch_at)"></dd>
                        </div>
                        <div class="shipping-quote__meta-row" x-show="quote.distance_km">
                            <dt>Distancia aprox.</dt>
                            <dd x-text="quote.distance_km + ' km'"></dd>
                        </div>
                    </dl>

                    <template x-if="quote.warnings && quote.warnings.length">
                        <ul class="shipping-warnings" aria-label="Avisos de entrega">
                            <template x-for="warning in quote.warnings" :key="warning.id">
                                <li
                                    class="shipping-warning"
                                    :class="'shipping-warning--' + (warning.severity || 'info')"
                                >
                                    <strong class="shipping-warning__title" x-text="warning.title"></strong>
                                    <span class="shipping-warning__message" x-text="warning.message"></span>
                                </li>
                            </template>
                        </ul>
                    </template>
                </div>
            </template>

            <template x-if="!quote && !loading && !error && municipalityId">
                <p class="shipping-quote__status text-muted">Selecciona un municipio para ver la cotización.</p>
            </template>
        </div>

        <p class="shipping-quote__footnote text-muted">
            Los valores son estimados. El costo final se confirma en el checkout al elegir tu municipio.
            <a href="{{ route('shipping') }}">Más sobre envíos</a>.
        </p>
    </div>
</div>
@endsection
