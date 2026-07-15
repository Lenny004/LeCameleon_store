@extends('layouts.store')

@section('title', 'Cuidado vintage — Le Cameleon')

@section('content')
{{-- Practical care tips for second-hand garments and objects. --}}
<div class="container" style="padding-block:var(--space-2xl);max-width:46rem;">
    <p class="text-small text-muted" style="letter-spacing:0.12em;text-transform:uppercase;margin-bottom:var(--space-sm);">Guía</p>
    <h1 class="heading-2" style="margin-bottom:var(--space-lg);">Cuidado de piezas vintage</h1>

    <div class="checkout-section" style="display:flex;flex-direction:column;gap:var(--space-lg);">
        <p class="text-lead">
            Las prendas y objetos vintage ya vivieron una vida. Con buen cuidado pueden acompañarte muchos años más.
        </p>

        <h2 class="text-small" style="font-weight:700;">Antes del primer uso</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
            <li>Revisa costuras, cremalleras y forros con luz natural.</li>
            <li>Prueba la talla con las medidas publicadas (no solo la etiqueta).</li>
            <li>Si hay olores leves de almacén, ventila en sombra 24–48 horas.</li>
        </ul>

        <h2 class="text-small" style="font-weight:700;">Lavado y limpieza</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
            <li>Prioriza lavado a mano en agua fría o tintorería especializada.</li>
            <li>Evita secadora: el calor acelera el desgaste de fibras antiguas.</li>
            <li>Para cuero y ante, usa productos específicos y paño seco.</li>
            <li>Sedas y lanas delicadas: siempre limpieza profesional si hay duda.</li>
        </ul>

        <h2 class="text-small" style="font-weight:700;">Almacenamiento</h2>
        <ul class="text-muted" style="padding-left:1.25rem;display:flex;flex-direction:column;gap:var(--space-xs);">
            <li>Usa perchas acolchadas para chaquetas y abrigos.</li>
            <li>Guarda lejos de luz solar directa y humedad.</li>
            <li>Introduce bolsas de tela o papel ácido libre; evita plástico hermético.</li>
        </ul>

        <p>
            ¿Dudas sobre una pieza concreta?
            <a href="{{ route('contact.show') }}">Escríbenos</a>
            y te orientamos según material y época.
        </p>
    </div>
</div>
@endsection
