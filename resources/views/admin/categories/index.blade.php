@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Product categories</h2>
    @if (Route::has('admin.categories.store'))
        <button type="button" class="btn btn--primary" onclick="document.getElementById('cat-form').style.display='block'">Add category</button>
    @endif
</div>

@if (Route::has('admin.categories.store'))
    <form id="cat-form" method="POST" action="{{ route('admin.categories.store') }}" class="card" style="display:none;margin-bottom:var(--space-xl);max-width:24rem;">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <button type="submit" class="btn btn--primary">Save</button>
    </form>
@endif

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Products</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories ?? [
                ['name' => 'Clothing', 'slug' => 'clothing', 'count' => 48],
                ['name' => 'Accessories', 'slug' => 'accessories', 'count' => 32],
                ['name' => 'Footwear', 'slug' => 'footwear', 'count' => 18],
                ['name' => 'Home', 'slug' => 'home', 'count' => 12],
            ] as $cat)
                <tr>
                    <td>{{ $cat['name'] }}</td>
                    <td>{{ $cat['slug'] }}</td>
                    <td>{{ $cat['count'] }}</td>
                    <td>
                        @if (Route::has('admin.categories.destroy'))
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat['slug']) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
