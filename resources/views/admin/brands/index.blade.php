@extends('layouts.admin')

@section('title', 'Brands')
@section('page-title', 'Brands')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Vintage brands</h2>
    <a href="{{ route('admin.brands.create') }}" class="btn btn--primary">Add brand</a>
</div>

<div class="table-wrap">
    <table class="table admin-table">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Slug</th>
                <th>Products</th>
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
                        <a class="admin-table__link btn btn--ghost btn--sm" href="{{ route('admin.brands.edit', $brand) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand?');" class="admin-inline-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-muted">No brands found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $brands->links() }}
@endsection
