@extends('layouts.admin')

@section('title', 'Matriz de tarifas')
@section('page-title', 'Tarifas de origen a destino')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Matriz de tarifas</h2>
        <span class="sr-only" aria-hidden="true">Rates matrix</span>
        <p class="admin-page-header__subtitle">Tarifa base desde la zona de origen hacia la zona de destino.</p>
    </div>
    <a href="{{ route('admin.logistics.zones.index') }}" class="btn btn--ghost">Volver a zonas</a>
</div>
@if ($zones->isEmpty())
    <p class="text-muted">Crea primero las zonas de envío.</p>
@else
    <form method="POST" action="{{ route('admin.logistics.zones.rates-matrix.update') }}" class="card">
        @csrf
        @method('PUT')
        <div class="logistics-rates-matrix">
            <table class="logistics-rates-matrix__table">
                <thead>
                    <tr class="logistics-rates-matrix__row">
                        <th class="logistics-rates-matrix__cell logistics-rates-matrix__header-cell logistics-rates-matrix__cell--sticky">
                            Origen / destino
                        </th>
                        @foreach ($zones as $dest)
                            <th class="logistics-rates-matrix__cell logistics-rates-matrix__header-cell">
                                {{ $dest->code }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
        @foreach ($zones as $origin)
            <tr class="logistics-rates-matrix__row">
                <th class="logistics-rates-matrix__cell logistics-rates-matrix__header-cell logistics-rates-matrix__cell--sticky">
                    {{ $origin->code }}
                </th>
            @foreach ($zones as $dest)
                @php $key = "{$origin->id}:{$dest->id}"; $fieldId = "rate_{$origin->id}_{$dest->id}"; $errorKey = "rates.{$key}"; $errorId = "{$fieldId}-error"; @endphp
                <td class="logistics-rates-matrix__cell">
                    <label class="sr-only" for="{{ $fieldId }}">Tarifa de {{ $origin->code }} a {{ $dest->code }}</label>
                    <input
                        type="number"
                        id="{{ $fieldId }}"
                        name="rates[{{ $key }}]"
                        class="form-input logistics-rates-matrix__input @error($errorKey) form-input--error @enderror"
                        value="{{ old($errorKey, $rates->get($key)?->base_fee) }}"
                        min="0"
                        max="99999999.99"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="0.00"
                        @error($errorKey) aria-invalid="true" aria-describedby="{{ $errorId }}" @enderror
                    >
                    @error($errorKey)<span class="form-error" id="{{ $errorId }}">{{ $message }}</span>@enderror
                </td>
            @endforeach
            </tr>
        @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn--primary admin-form__submit admin-form__submit--spaced">Guardar matriz</button>
    </form>
@endif
@endsection
