@extends('layouts.admin')

@section('title', 'Zone rates matrix')
@section('page-title', 'Origin → destination fees')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Rates matrix</h2>
        <p class="admin-page-header__subtitle">Base fee from origin zone (rows) to destination zone (columns).</p>
    </div>
    <a href="{{ route('admin.logistics.zones.index') }}" class="btn btn--ghost">Back to zones</a>
</div>

@if ($zones->isEmpty())
    <p class="text-muted">Create shipping zones first.</p>
@else
    <form method="POST" action="{{ route('admin.logistics.zones.rates-matrix.update') }}" class="card">
        @csrf
        @method('PUT')
        <div class="logistics-rates-matrix">
            <table class="logistics-rates-matrix__table">
                <thead>
                    <tr>
                        <th>Origin \ Dest</th>
                        @foreach ($zones as $dest)
                            <th>{{ $dest->code }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($zones as $origin)
                        <tr>
                            <th>{{ $origin->code }}</th>
                            @foreach ($zones as $dest)
                                @php $key = "{$origin->id}:{$dest->id}"; @endphp
                                <td>
                                    <input type="number" step="0.01" min="0" name="rates[{{ $key }}]" class="form-input logistics-rates-matrix__input" value="{{ old("rates.{$key}", $rates->get($key)?->base_fee) }}">
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn--primary" style="margin-top:var(--space-lg);">Save matrix</button>
    </form>
@endif
@endsection
