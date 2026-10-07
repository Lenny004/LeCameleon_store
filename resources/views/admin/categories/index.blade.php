@extends('layouts.admin')

@section('title', 'Categories')
@section('page-title', 'Categories')

@section('content')
<div class="admin-page-header">
    <h2 class="admin-page-header__title">Product categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn--primary">Add category</a>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Parent</th>
                <th>Slug</th>
                <th>Products</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td><a href="{{ route('admin.categories.show', $category) }}">{{ $category->name }}</a></td>
                    <td>{{ $category->parent?->name ?? '—' }}</td>
                    <td><code>{{ $category->slug }}</code></td>
                    <td>{{ $category->products_count }}</td>
                    <td><span class="badge badge--{{ $category->is_active ? 'success' : 'warning' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn--ghost btn--sm">Edit</a>
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-muted">No categories found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $categories->links() }}
@endsection
