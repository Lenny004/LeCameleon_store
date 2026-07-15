@extends('layouts.store')

@section('title', 'Rastreo ' . $code . ' — Le Cameleon')

@section('content')
@php
    // Spanish labels for shipment lifecycle statuses (storefront copy).
    $statusLabels = [
        'pending' => 'Pendiente',
        'scheduled' => 'Programado',
        'shipped' => 'Enviado',
        'picked_up' => 'Recolectado',
        'in_transit' => 'En tránsito',
        'out_for_delivery' => 'En reparto',
        'delivered' => 'Entregado',
        'failed' => 'Entrega fallida',
        'returned' => 'Devuelto',
        'returned_to_warehouse' => 'Devuelto a bodega',
        'cancelled' => 'Cancelado',
    ];

    $outcomeLabels = [
        'accepted' => 'Recibido por el destinatario',
        'refused' => 'Rechazado por el destinatario',
        'no_answer' => 'Sin respuesta en domicilio',
        'wrong_address' => 'Dirección incorrecta',
        'rescheduled' => 'Entrega reprogramada',
        'left_with_neighbor' => 'Entregado a un vecino',
    ];

    $municipality = $shipment?->destinationMunicipality;
    $latestOutcome = $shipment?->events
        ->filter(fn ($event) => $event->recipient_outcome !== null)
        ->last();
    $currentStatus = $shipment?->status?->value;
@endphp

<div class="container tracking">
    <nav class="breadcrumb">
        <a href="{{ route('tracking.index') }}">Rastrear envío</a>
        <span class="breadcrumb__sep">/</span>
        <span>{{ $code }}</span>
    </nav>

    @unless ($shipment)
        <div class="empty-state" style="margin-top:var(--space-xl);">
            <h1 class="heading-2">No encontramos ese código</h1>
            <p class="text-muted">
                Verifica que el código <strong class="tracking-status__code">{{ $code }}</strong> sea correcto
                e inténtalo de nuevo.
            </p>
            <a href="{{ route('tracking.index') }}" class="btn btn--primary" style="margin-top:var(--space-md);">
                Volver a buscar
            </a>
        </div>
    @else
        <div class="tracking-status">
            <p class="text-small text-muted" style="margin:0 0 var(--space-xs);">Código de rastreo</p>
            <p class="tracking-status__code">{{ $shipment->tracking_number }}</p>

            @if ($shipment->carrier)
                <p class="text-muted" style="margin:var(--space-sm) 0 0;">
                    Transportista: <strong>{{ $shipment->carrier }}</strong>
                </p>
            @endif

            @if ($municipality)
                <p class="text-muted" style="margin:var(--space-xs) 0 0;">
                    Destino:
                    <strong>{{ $municipality->name }}</strong>
                    @if ($municipality->department)
                        ({{ $municipality->department->name }})
                    @endif
                </p>
            @endif

            <span class="tracking-status__badge">
                {{ $statusLabels[$currentStatus] ?? ucfirst(str_replace('_', ' ', $currentStatus ?? '')) }}
            </span>
        </div>

        @if ($latestOutcome)
            <div class="tracking-outcome">
                <p class="tracking-outcome__label">Resultado de entrega</p>
                <p style="margin:0;font-weight:600;">
                    {{ $outcomeLabels[$latestOutcome->recipient_outcome->value] ?? $latestOutcome->recipient_outcome->value }}
                </p>
                @if ($latestOutcome->note)
                    <p class="text-muted" style="margin:var(--space-xs) 0 0;font-size:0.875rem;">{{ $latestOutcome->note }}</p>
                @endif
            </div>
        @endif

        @if ($shipment->events->isNotEmpty())
            <section aria-label="Historial del envío">
                <h2 class="text-small" style="font-weight:700;margin:var(--space-xl) 0 var(--space-sm);">Historial</h2>
                <ol class="tracking-timeline">
                    @foreach ($shipment->events as $event)
                        @php
                            $eventStatus = $event->status?->value;
                            $isCurrent = $eventStatus === $currentStatus && $loop->last;
                        @endphp
                        <li class="tracking-timeline__item {{ $isCurrent ? 'tracking-timeline__item--current' : 'tracking-timeline__item--done' }}">
                            <span class="tracking-timeline__status">
                                {{ $statusLabels[$eventStatus] ?? ucfirst(str_replace('_', ' ', $eventStatus ?? '')) }}
                            </span>
                            @if ($event->happened_at)
                                <time class="tracking-timeline__time" datetime="{{ $event->happened_at->toIso8601String() }}">
                                    {{ $event->happened_at->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
                                </time>
                            @endif
                            @if ($event->recipient_outcome)
                                <p class="tracking-timeline__note">
                                    {{ $outcomeLabels[$event->recipient_outcome->value] ?? $event->recipient_outcome->value }}
                                </p>
                            @endif
                            @if ($event->note)
                                <p class="tracking-timeline__note">{{ $event->note }}</p>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif

        @if ($municipality?->latitude && $municipality?->longitude)
            <section class="tracking-map" aria-label="Ubicación aproximada del destino">
                <h2 class="text-small" style="font-weight:700;margin-bottom:var(--space-sm);">Ubicación del destino</h2>
                <div class="tracking-map__placeholder" role="img" aria-label="Mapa aproximado del municipio de destino">
                    Vista previa del mapa — {{ $municipality->name }}
                </div>
                <p class="tracking-map__coords">
                    {{ number_format((float) $municipality->latitude, 5) }},
                    {{ number_format((float) $municipality->longitude, 5) }}
                </p>
            </section>
        @endif

        <p style="margin-top:var(--space-xl);">
            <a href="{{ route('tracking.index') }}" class="btn btn--secondary">Buscar otro código</a>
        </p>
    @endunless
</div>
@endsection
