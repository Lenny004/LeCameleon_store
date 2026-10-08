@extends('layouts.admin')

@section('title', 'Ofertas')
@section('page-title', 'Ofertas')
@section('page-subtitle', 'Revisa las propuestas de precio de las personas compradoras')

@section('content')
<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cliente</th>
                <th>Oferta</th>
                <th>Contraoferta</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($offers as $offer)
                <tr>
                    <td>
                        <a class="admin-table__link" href="{{ $offer->product ? url('/shop/'.$offer->product->slug) : '#' }}">
                            {{ $offer->product?->name ?? '—' }}
                        </a>
                        @if ($offer->product)
                            <br><span class="text-muted text-small">Precio de lista: ${{ number_format((float) $offer->product->price, 2) }}</span>
                        @endif
                    </td>
                    <td>{{ $offer->user?->email ?? '—' }}</td>
                    <td>${{ number_format((float) $offer->amount, 2) }}</td>
                    <td>
                        @if ($offer->counter_amount)
                            ${{ number_format((float) $offer->counter_amount, 2) }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @php
                            $statusBadge = match ($offer->status->value) {
                                'accepted' => 'success',
                                'declined' => 'danger',
                                'countered' => 'primary',
                                default => 'warning',
                            };
                        @endphp
                        <span class="badge badge--{{ $statusBadge }}">
                            {{ [
                                'pending' => 'Pendiente',
                                'accepted' => 'Aceptada',
                                'declined' => 'Rechazada',
                                'countered' => 'Contraofertada',
                                'expired' => 'Vencida',
                                'withdrawn' => 'Retirada',
                            ][$offer->status->value] ?? $offer->status->value }}
                        </span>
                    </td>
                    <td>{{ $offer->created_at?->format('Y-m-d') }}</td>
                    <td>
                        @if ($offer->status->value === 'pending')
                            <div class="admin-table__actions admin-table__actions--stacked">
                                @if (Route::has('admin.offers.accept'))
                                    <form method="POST" action="{{ route('admin.offers.accept', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <label class="sr-only" for="accept-notes-{{ $offer->id }}">Notas administrativas</label>
                                        <input
                                            type="text"
                                            id="accept-notes-{{ $offer->id }}"
                                            name="admin_notes"
                                            class="form-input form-input--sm admin-offer__input @error('admin_notes') form-input--error @enderror"
                                            value="{{ old('admin_notes') }}"
                                            placeholder="Nota opcional para la aceptación"
                                            maxlength="2000"
                                            @error('admin_notes') aria-invalid="true" aria-describedby="accept-notes-error-{{ $offer->id }}" @enderror
                                        >
                                        @error('admin_notes')<span class="form-error" id="accept-notes-error-{{ $offer->id }}">{{ $message }}</span>@enderror
                                        <button type="submit" class="btn btn--ghost btn--sm">Aceptar</button>
                                    </form>
                                @endif
                                @if (Route::has('admin.offers.decline'))
                                    <form method="POST" action="{{ route('admin.offers.decline', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn--ghost btn--sm">Rechazar</button>
                                    </form>
                                @endif
                                @if (Route::has('admin.offers.counter'))
                                    <form method="POST" action="{{ route('admin.offers.counter', $offer) }}">
                                        @csrf
                                        @method('PATCH')
                                        <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
                                        <label class="sr-only" for="counter-amount-{{ $offer->id }}">Importe de la contraoferta <span class="form-label__required" aria-hidden="true">*</span></label>
                                        <input
                                            type="number"
                                            id="counter-amount-{{ $offer->id }}"
                                            name="counter_amount"
                                            class="form-input form-input--sm admin-offer__input @error('counter_amount') form-input--error @enderror"
                                            value="{{ old('counter_amount') }}"
                                            step="0.01"
                                            min="1"
                                            max="9999999999.99"
                                            inputmode="decimal"
                                            placeholder="25.00"
                                            required
                                            @error('counter_amount') aria-invalid="true" aria-describedby="counter-amount-error-{{ $offer->id }}" @enderror
                                        >
                                        @error('counter_amount')<span class="form-error" id="counter-amount-error-{{ $offer->id }}">{{ $message }}</span>@enderror
                                        <label class="sr-only" for="counter-notes-{{ $offer->id }}">Notas administrativas</label>
                                        <input
                                            type="text"
                                            id="counter-notes-{{ $offer->id }}"
                                            name="admin_notes"
                                            class="form-input form-input--sm admin-offer__input @error('admin_notes') form-input--error @enderror"
                                            value="{{ old('admin_notes') }}"
                                            placeholder="Nota opcional para la contraoferta"
                                            maxlength="2000"
                                            @error('admin_notes') aria-invalid="true" aria-describedby="counter-notes-error-{{ $offer->id }}" @enderror
                                        >
                                        @error('admin_notes')<span class="form-error" id="counter-notes-error-{{ $offer->id }}">{{ $message }}</span>@enderror
                                        <button type="submit" class="btn btn--ghost btn--sm">Contraofertar</button>
                                    </form>
                                @endif
                            </div>
                        @elseif ($offer->admin_notes)
                            <span class="text-muted text-small">{{ Str::limit($offer->admin_notes, 40) }}</span>
                        @endif
                        @if ($offer->message)
                            <p class="text-muted text-small admin-offer__message">"{{ Str::limit($offer->message, 60) }}"</p>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                <td colspan="7" class="text-muted">Aún no hay ofertas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($offers->hasPages())
    <div class="admin-pagination">
        {{ $offers->links() }}
    </div>
@endif
@endsection
