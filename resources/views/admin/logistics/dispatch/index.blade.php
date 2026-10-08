@extends('layouts.admin')

@section('title', 'Horarios de despacho')
@section('page-title', 'Horarios de despacho')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Horarios de despacho</h2>
        <p class="admin-page-header__subtitle">Ventana del próximo despacho y hora límite de pedidos.</p>
    </div>
</div>

<div class="card admin-panel admin-panel--spaced">
    <h2 class="card__title logistics-form__heading">Agregar horario</h2>
    <form method="POST" action="{{ route('admin.logistics.dispatch.store') }}" class="logistics-form__grid">
    @csrf
    <p class="form-required-note logistics-form__full">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    <div class="form-group">
        <label class="form-label" for="name">Nombre <span class="form-label__required" aria-hidden="true">*</span></label>
        <input
            type="text"
            id="name"
            name="name"
            class="form-input @error('name') form-input--error @enderror"
            value="{{ old('name') }}"
            placeholder="Despacho de la mañana"
            maxlength="150"
            required
            autocomplete="off"
            @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
        >
        @error('name')<span class="form-error" id="name-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="next_dispatch_at">Próximo despacho <span class="form-label__required" aria-hidden="true">*</span></label>
        <input
            type="datetime-local"
            id="next_dispatch_at"
            name="next_dispatch_at"
            class="form-input @error('next_dispatch_at') form-input--error @enderror"
            value="{{ old('next_dispatch_at') }}"
            required
            @error('next_dispatch_at') aria-invalid="true" aria-describedby="next_dispatch_at-error" @enderror
        >
        @error('next_dispatch_at')<span class="form-error" id="next_dispatch_at-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label class="form-label" for="cutoff_at">Hora límite <span class="form-label__required" aria-hidden="true">*</span></label>
        <input
            type="datetime-local"
            id="cutoff_at"
            name="cutoff_at"
            class="form-input @error('cutoff_at') form-input--error @enderror"
            value="{{ old('cutoff_at') }}"
            required
            @error('cutoff_at') aria-invalid="true" aria-describedby="cutoff_at-error" @enderror
        >
        @error('cutoff_at')<span class="form-error" id="cutoff_at-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group logistics-form__full">
        <button type="submit" class="btn btn--primary">Crear horario</button>
    </div>
    </form>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Próximo despacho</th>
                <th>Hora límite</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->name }}</td>
                    <td>{{ $schedule->next_dispatch_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $schedule->cutoff_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <span class="badge badge--{{ $schedule->is_active ? 'success' : 'warning' }}">
                            {{ $schedule->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.logistics.dispatch.destroy', $schedule) }}" onsubmit="return confirm('¿Eliminar este horario?');" class="admin-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-muted">Aún no hay horarios.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $schedules->links() }}
@endsection
