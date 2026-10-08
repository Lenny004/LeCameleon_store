@extends('layouts.admin')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="admin-form admin-form--medium">
    @csrf
    @method('PUT')

    <div class="card">
        <h2 class="card__title admin-form__title">Store</h2>
        <div class="admin-form__fields">
            <div class="form-group">
                <label class="form-label" for="store_name">Store name</label>
                <input type="text" id="store_name" name="store_name" class="form-input" value="{{ old('store_name', $settings['store_name']) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_email">Contact email</label>
                <input type="email" id="contact_email" name="contact_email" class="form-input" value="{{ old('contact_email', $settings['contact_email']) }}">
            </div>
            <div class="form-group">
                <label class="form-label" for="contact_phone">Contact phone</label>
                <input type="text" id="contact_phone" name="contact_phone" class="form-input" value="{{ old('contact_phone', $settings['contact_phone']) }}">
            </div>
        </div>
    </div>

    <div class="card">
        <h2 class="card__title admin-form__title">Returns</h2>
        <div class="form-group">
            <label class="form-label" for="returns_policy">Returns policy</label>
            <textarea id="returns_policy" name="returns_policy" class="form-textarea" rows="10">{{ old('returns_policy', $settings['returns_policy']) }}</textarea>
        </div>
    </div>

    <p class="text-muted">These values come from the settings table and are used by the storefront layout and returns page. Currency, taxes and shipping calculation remain operational configuration in <code>config/store.php</code> and environment variables.</p>

    <button type="submit" class="btn btn--primary admin-form__submit">Save settings</button>
</form>
@endsection
