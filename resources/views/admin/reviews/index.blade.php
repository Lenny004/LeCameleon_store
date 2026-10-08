@extends('layouts.admin')

@section('title', 'Reseñas')
@section('page-title', 'Reseñas')
@section('page-subtitle', 'Modera los comentarios de clientes')

@section('content')
<form method="GET" action="{{ route('admin.reviews.index') }}" class="card admin-filters admin-filters--grid">
    <div class="form-group admin-filters__field">
        <label class="form-label" for="q">Búsqueda</label>
        <input
            type="search"
            id="q"
            name="q"
            class="form-input @error('q') form-input--error @enderror"
            value="{{ old('q', $filters['q'] ?? '') }}"
            placeholder="Producto, cliente o texto…"
            maxlength="150"
            inputmode="search"
            autocomplete="off"
            @error('q') aria-invalid="true" aria-describedby="q-error" @enderror
        >
        @error('q')<span class="form-error" id="q-error">{{ $message }}</span>@enderror
    </div>
    <div class="form-group admin-filters__field">
        <label class="form-label" for="status">Estado</label>
        <select id="status" name="status" class="form-select">
            <option value="">Todas</option>
            <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pendientes</option>
            <option value="approved" @selected(($filters['status'] ?? '') === 'approved')>Aprobadas</option>
        </select>
    </div>
    <div class="form-group admin-filters__field">
        <label class="form-label" for="rating">Calificación</label>
        <select id="rating" name="rating" class="form-select">
            <option value="">Todas</option>
            @for ($i = 5; $i >= 1; $i--)
                <option value="{{ $i }}" @selected((string) ($filters['rating'] ?? '') === (string) $i)>{{ $i }} ★</option>
            @endfor
        </select>
    </div>
    <div class="admin-actions">
        <button type="submit" class="btn btn--primary">Filtrar</button>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn--ghost">Limpiar</a>
    </div>
</form>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cliente</th>
                <th>Calificación</th>
                <th>Comentario</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reviews as $review)
                <tr>
                    <td>
                        @if ($review->product)
                            <a class="admin-table__link" href="{{ route('admin.products.show', $review->product) }}">{{ $review->product->name }}</a>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        {{ $review->user?->name ?? '—' }}
                        @if ($review->user?->email)
                            <br><span class="text-muted text-small">{{ $review->user->email }}</span>
                        @endif
                    </td>
                    <td>{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</td>
                    <td>
                        @if ($review->title)
                            <strong>{{ $review->title }}</strong><br>
                        @endif
                        <span class="text-muted">{{ Str::limit($review->body, 80) }}</span>
                        @if ($review->is_verified_purchase)
                            <br><span class="badge badge--success">Compra verificada</span>
                        @endif
                        <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" class="admin-form__reply">
                            @csrf
                            @method('PATCH')
                            <label class="form-label" for="store_reply_{{ $review->id }}">Respuesta de la tienda</label>
                            <textarea id="store_reply_{{ $review->id }}" name="store_reply" class="form-textarea" rows="2" maxlength="2000" placeholder="Gracias por compartir tu experiencia.">{{ old('store_reply', $review->store_reply) }}</textarea>
                            <button type="submit" class="btn btn--ghost btn--sm">{{ $review->store_reply ? 'Actualizar respuesta' : 'Responder' }}</button>
                        </form>
                        @if ($review->store_reply)
                            <form method="POST" action="{{ route('admin.reviews.reply', $review) }}" x-on:submit="if (!confirm('¿Eliminar la respuesta de la tienda?')) $event.preventDefault()">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="store_reply" value="">
                                <button type="submit" class="btn btn--ghost btn--sm">Eliminar respuesta</button>
                            </form>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge--{{ $review->is_approved ? 'success' : 'warning' }}">
                            {{ $review->is_approved ? 'Aprobada' : 'Pendiente' }}
                        </span>
                    </td>
                    <td>{{ $review->created_at?->format('Y-m-d') }}</td>
                    <td>
                        <div class="admin-table__actions">
                            @unless ($review->is_approved)
                                <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn--ghost btn--sm">Aprobar</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.reject', $review) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn--ghost btn--sm">Rechazar</button>
                                </form>
                            @endunless
                            <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" x-on:submit="if (!confirm('¿Eliminar esta reseña?')) $event.preventDefault()">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--ghost btn--sm">Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                <td colspan="7" class="text-muted">Ninguna reseña coincide con estos filtros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($reviews->hasPages())
    <div class="admin-pagination">
        {{ $reviews->links() }}
    </div>
@endif
@endsection
