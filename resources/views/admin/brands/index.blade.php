@extends('layouts.admin')

@section('title', 'Marcas')
@section('page-title', 'Marcas')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Marcas vintage</h2>
    <a href="{{ route('admin.brands.create') }}" class="btn btn--primary">Añadir marca</a>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Marca</th>
                <th>Slug</th>
                <th>Productos</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($brands as $brand)
                <tr>
                    <td><a class="admin-table__link" href="{{ route('admin.brands.show', $brand) }}">{{ $brand->name }}</a></td>
                    <td><code>{{ $brand->slug }}</code></td>
                    <td>{{ $brand->products_count }}</td>
                    <td>
                        <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.brands.edit', $brand) }}">Editar</a>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" x-on:submit="if (!confirm('¿Eliminar esta marca?')) $event.preventDefault()" class="admin-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm" aria-label="Eliminar marca {{ $brand->name }}">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted">No se encontraron marcas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $brands->links() }}
@endsection
