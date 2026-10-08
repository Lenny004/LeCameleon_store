@extends('layouts.admin')

@section('title', 'Advertencias de entrega')
@section('page-title', 'Advertencias de entrega')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Advertencias de entrega</h2>
        <p class="admin-page-header__subtitle">Avisos para el checkout, seguimiento o administración.</p>
    </div>
</div>

<div class="card admin-panel admin-panel--spaced">
    <h2 class="card__title logistics-form__heading">Agregar advertencia</h2>
    <form method="POST" action="{{ route('admin.logistics.warnings.store') }}" class="logistics-form__grid">
    @csrf
    <p class="form-required-note logistics-form__full">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    <div class="form-group">
        <label class="form-label" for="code">Código <span class="form-label__required" aria-hidden="true">*</span></label>
        <input
            type="text"
            id="code"
            name="code"
            class="form-input @error('code') form-input--error @enderror"
            value="{{ old('code') }}"
            placeholder="CLIMA-LLUVIA"
            maxlength="50"
            required
            autocomplete="off"
            @error('code') aria-invalid="true" aria-describedby="code-error" @enderror
        >
        @error('code')<span class="form-error" id="code-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="title">Título <span class="form-label__required" aria-hidden="true">*</span></label>
        <input
            type="text"
            id="title"
            name="title"
            class="form-input @error('title') form-input--error @enderror"
            value="{{ old('title') }}"
            placeholder="Retrasos por lluvia"
            maxlength="200"
            required
            autocomplete="off"
            @error('title') aria-invalid="true" aria-describedby="title-error" @enderror
        >
        @error('title')<span class="form-error" id="title-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="severity">Severidad <span class="form-label__required" aria-hidden="true">*</span></label>
        <select
            id="severity"
            name="severity"
            class="form-select @error('severity') form-select--error @enderror"
            required
            @error('severity') aria-invalid="true" aria-describedby="severity-error" @enderror
        >
            <option value="" disabled {{ old('severity') === null ? 'selected' : '' }}>Selecciona una severidad</option>
            @foreach ($severities as $severity)
                <option value="{{ $severity->value }}" @selected(old('severity') === $severity->value)>
                    {{ ['info' => 'Información', 'warning' => 'Advertencia', 'danger' => 'Peligro'][$severity->value] }}
                </option>
            @endforeach
        </select>
        @error('severity')<span class="form-error" id="severity-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="applies_to">Aplica a <span class="form-label__required" aria-hidden="true">*</span></label>
        <select
            id="applies_to"
            name="applies_to"
            class="form-select @error('applies_to') form-select--error @enderror"
            required
            @error('applies_to') aria-invalid="true" aria-describedby="applies_to-error" @enderror
        >
            <option value="" disabled {{ old('applies_to') === null ? 'selected' : '' }}>Selecciona un destino</option>
            @foreach ($appliesTo as $scope)
                <option value="{{ $scope->value }}" @selected(old('applies_to') === $scope->value)>
                    {{ ['checkout' => 'Checkout', 'tracking' => 'Seguimiento', 'admin' => 'Administración'][$scope->value] }}
                </option>
            @endforeach
        </select>
        @error('applies_to')<span class="form-error" id="applies_to-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group logistics-form__full">
        <label class="form-label" for="body">Mensaje <span class="form-label__required" aria-hidden="true">*</span></label>
        <textarea
            id="body"
            name="body"
            class="form-textarea @error('body') form-textarea--error @enderror"
            rows="4"
            maxlength="5000"
            placeholder="Explica el aviso para las personas compradoras."
            required
            @error('body') aria-invalid="true" aria-describedby="body-error" @enderror
        >{{ old('body') }}</textarea>
        @error('body')<span class="form-error" id="body-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group logistics-form__full"><button type="submit" class="btn btn--primary">Crear advertencia</button></div>
</form>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Título</th>
                <th>Severidad</th>
                <th>Destino</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warnings as $warning)
                <tr>
                    <td><code>{{ $warning->code }}</code></td>
                    <td>{{ $warning->title }}</td>
                    <td>{{ ['info' => 'Información', 'warning' => 'Advertencia', 'danger' => 'Peligro'][$warning->severity->value] }}</td>
                    <td>{{ ['checkout' => 'Checkout', 'tracking' => 'Seguimiento', 'admin' => 'Administración'][$warning->applies_to->value] }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.logistics.warnings.destroy', $warning) }}" x-on:submit="if (!confirm('¿Eliminar esta advertencia?')) $event.preventDefault()" class="admin-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-muted">Aún no hay advertencias.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $warnings->links() }}
@endsection
