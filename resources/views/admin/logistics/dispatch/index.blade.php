@extends('layouts.admin')

@section('title', 'Dispatch schedules')
@section('page-title', 'Dispatch schedules')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Dispatch schedules</h2>
        <p class="admin-page-header__subtitle">Next dispatch window and order cutoff times.</p>
    </div>
</div>

<div class="card" style="margin-bottom:var(--space-xl);">
    <h2 class="card__title logistics-form__heading">Add schedule</h2>
    <form method="POST" action="{{ route('admin.logistics.dispatch.store') }}" class="logistics-form__grid">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="next_dispatch_at">Next dispatch at</label>
            <input type="datetime-local" id="next_dispatch_at" name="next_dispatch_at" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="cutoff_at">Cutoff at</label>
            <input type="datetime-local" id="cutoff_at" name="cutoff_at" class="form-input" required>
        </div>
        <div class="form-group logistics-form__full">
            <button type="submit" class="btn btn--primary">Create schedule</button>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Next dispatch</th>
                <th>Cutoff</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->name }}</td>
                    <td>{{ $schedule->next_dispatch_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $schedule->cutoff_at?->format('Y-m-d H:i') }}</td>
                    <td><span class="badge badge--{{ $schedule->is_active ? 'success' : 'warning' }}">{{ $schedule->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>
                        <form method="POST" action="{{ route('admin.logistics.dispatch.destroy', $schedule) }}" onsubmit="return confirm('Remove?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No schedules yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $schedules->links() }}
@endsection
