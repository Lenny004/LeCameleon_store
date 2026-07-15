@extends('layouts.admin')

@section('title', 'Reviews')
@section('page-title', 'Reviews')
@section('page-subtitle', 'Moderate customer feedback')

@section('content')
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Customer</th>
                <th>Rating</th>
                <th>Comment</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reviews ?? [
                ['product' => 'Denim Jacket 80s', 'customer' => 'María G.', 'rating' => 5, 'comment' => 'Pieza increíble, tal como en fotos.', 'status' => 'approved'],
                ['product' => 'Floral Dress 70s', 'customer' => 'Ana L.', 'rating' => 4, 'comment' => 'Muy bonito, llegó rápido.', 'status' => 'pending'],
            ] as $review)
                <tr>
                    <td>{{ $review['product'] }}</td>
                    <td>{{ $review['customer'] }}</td>
                    <td>{{ str_repeat('★', $review['rating']) }}</td>
                    <td>{{ Str::limit($review['comment'], 40) }}</td>
                    <td>
                        <span class="badge badge--{{ $review['status'] === 'approved' ? 'success' : 'warning' }}">
                            {{ ucfirst($review['status']) }}
                        </span>
                    </td>
                    <td>
                        <div class="table__actions">
                            @if (Route::has('admin.reviews.approve'))
                                <form method="POST" action="{{ route('admin.reviews.approve', 1) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn--ghost btn--sm">Approve</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
