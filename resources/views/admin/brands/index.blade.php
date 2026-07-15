@extends('layouts.admin')

@section('title', 'Brands')
@section('page-title', 'Brands')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Vintage brands</h2>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Brand</th>
                <th>Slug</th>
                <th>Products</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($brands ?? [
                ['name' => "Levi's", 'slug' => 'levis', 'count' => 14],
                ['name' => 'Adidas', 'slug' => 'adidas', 'count' => 8],
                ['name' => 'Unknown / No label', 'slug' => 'no-label', 'count' => 42],
            ] as $brand)
                <tr>
                    <td>{{ $brand['name'] }}</td>
                    <td>{{ $brand['slug'] }}</td>
                    <td>{{ $brand['count'] }}</td>
                    <td>
                        @if (Route::has('admin.brands.edit'))
                            <a href="{{ route('admin.brands.edit', $brand['slug']) }}" class="btn btn--ghost btn--sm">Edit</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
