@extends('layouts.admin')

@section('title', 'Delivery warnings')
@section('page-title', 'Delivery warnings')

@section('content')
<div class="admin-page-header">
    <div>
        <h2 class="admin-page-header__title">Delivery warnings</h2>
        <p class="admin-page-header__subtitle">Advisories at checkout, tracking, or admin.</p>
    </div>
</div>

<div class="card" style="margin-bottom:var(--space-xl);">
    <h2 class="card__title logistics-form__heading">Add warning</h2>
    <form method="POST" action="{{ route('admin.logistics.warnings.store') }}" class="logistics-form__grid">
        @csrf
        <div class="form-group">
            <label class="form-label" for="code">Code</label>
            <input type="text" id="code" name="code" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="form-input" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="severity">Severity</label>
            <select id="severity" name="severity" class="form-select" required>
                @foreach ($severities as $severity)
                    <option value="{{ $severity->value }}">{{ ucfirst($severity->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="applies_to">Applies to</label>
            <select id="applies_to" name="applies_to" class="form-select" required>
                @foreach ($appliesTo as $scope)
                    <option value="{{ $scope->value }}">{{ ucfirst($scope->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group logistics-form__full">
            <label class="form-label" for="body">Message</label>
            <textarea id="body" name="body" class="form-textarea" required></textarea>
        </div>
        <div class="form-group logistics-form__full">
            <button type="submit" class="btn btn--primary">Create warning</button>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Severity</th>
                <th>Scope</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($warnings as $warning)
                <tr>
                    <td><code>{{ $warning->code }}</code></td>
                    <td>{{ $warning->title }}</td>
                    <td>{{ ucfirst($warning->severity->value) }}</td>
                    <td>{{ ucfirst($warning->applies_to->value) }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.logistics.warnings.destroy', $warning) }}" onsubmit="return confirm('Remove?');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn--ghost btn--sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-muted">No warnings yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $warnings->links() }}
@endsection
