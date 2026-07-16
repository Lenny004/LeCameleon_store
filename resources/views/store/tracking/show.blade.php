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
        @include('components.empty-state', [
            'title' => 'No encontramos ese código',
            'text' => 'Verifica que el código ' . $code . ' sea correcto e inténtalo de nuevo.',
            'actionLabel' => 'Volver a buscar',
            'actionUrl' => route('tracking.index'),
        ])
    @else
        <div class="tracking-status">
            <p class="tracking-status__label">Código de rastreo</p>
            <p class="tracking-status__code">{{ $shipment->tracking_number }}</p>

            @if ($shipment->carrier)
                <p class="tracking__meta-line">
                    Transportista: <strong>{{ $shipment->carrier }}</strong>
                </p>
            @endif

            @if ($municipality)
                <p class="tracking__meta-line tracking__meta-line--tight">
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
                <p class="tracking-outcome__text">
                    {{ $outcomeLabels[$latestOutcome->recipient_outcome->value] ?? $latestOutcome->recipient_outcome->value }}
                </p>
                @if ($latestOutcome->note)
                    <p class="order-card__note">{{ $latestOutcome->note }}</p>
                @endif
            </div>
        @endif

        @if ($shipment->events->isNotEmpty())
            <section aria-label="Historial del envío">
                <h2 class="tracking__section-heading">Historial</h2>
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
                <h2 class="tracking__section-heading">Ubicación del destino</h2>
                <div class="tracking-map__placeholder" role="img" aria-label="Mapa aproximado del municipio de destino">
                    Vista previa del mapa — {{ $municipality->name }}
                </div>
                <p class="tracking-map__coords">
                    {{ number_format((float) $municipality->latitude, 5) }},
                    {{ number_format((float) $municipality->longitude, 5) }}
                </p>
            </section>
        @endif

        <div class="tracking__actions">
            <a href="{{ route('tracking.index') }}" class="btn btn--secondary">Buscar otro código</a>
        </div>
    @endunless
</div>
@endsection
