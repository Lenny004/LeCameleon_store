@extends('layouts.admin')

@section('title', 'Categorías')
@section('page-title', 'Categorías')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Categorías de productos</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn--primary">Añadir categoría</a>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Superior</th>
                <th>Slug</th>
                <th>Productos</th>
                <th>Estado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td><a class="admin-table__link" href="{{ route('admin.categories.show', $category) }}">{{ $category->name }}</a></td>
                    <td>{{ $category->parent?->name ?? '—' }}</td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>{{ $category->products_count }}</td>
                        <td><span class="badge badge--{{ $category->is_active ? 'success' : 'warning' }}">{{ $category->is_active ? 'Activa' : 'Inactiva' }}</span></td>
                    <td>
                        <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.categories.edit', $category) }}">Editar</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" x-on:submit="if (!confirm('¿Eliminar esta categoría?')) $event.preventDefault()" class="admin-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm" aria-label="Eliminar categoría {{ $category->name }}">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No se encontraron categorías.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $categories->links() }}
@endsection
