<form method="POST" action="{{ $formAction }}" class="admin-form admin-form--medium">
    @csrf @if ($formMethod !== 'POST') @method($formMethod) @endif
    <p class="form-required-note">Los campos con <span class="form-label__required" aria-hidden="true">*</span> son obligatorios.</p>
    @error('addresses')
        <span class="form-error" id="addresses-error">{{ $message }}</span>
    @enderror
    @php($fields = [
        ['label' => 'Etiqueta', 'name' => 'label', 'type' => 'text', 'placeholder' => 'Casa o trabajo', 'autocomplete' => 'address-line2', 'required' => false, 'max' => 100],
        ['label' => 'Nombre', 'name' => 'first_name', 'type' => 'text', 'placeholder' => 'María', 'autocomplete' => 'given-name', 'required' => true, 'max' => 100],
        ['label' => 'Apellido', 'name' => 'last_name', 'type' => 'text', 'placeholder' => 'López', 'autocomplete' => 'family-name', 'required' => true, 'max' => 100],
        ['label' => 'Dirección', 'name' => 'line1', 'type' => 'text', 'placeholder' => 'Calle Principal 123', 'autocomplete' => 'address-line1', 'required' => true, 'max' => 255],
        ['label' => 'Referencia adicional', 'name' => 'line2', 'type' => 'text', 'placeholder' => 'Apartamento o punto cercano', 'autocomplete' => 'address-line2', 'required' => false, 'max' => 255],
        ['label' => 'Ciudad', 'name' => 'city', 'type' => 'text', 'placeholder' => 'San Salvador', 'autocomplete' => 'address-level2', 'required' => true, 'max' => 100],
        ['label' => 'Departamento', 'name' => 'state', 'type' => 'text', 'placeholder' => 'San Salvador', 'autocomplete' => 'address-level1', 'required' => false, 'max' => 100],
        ['label' => 'Código postal', 'name' => 'postal_code', 'type' => 'text', 'placeholder' => '1101', 'autocomplete' => 'postal-code', 'required' => true, 'max' => 20],
        ['label' => 'Teléfono', 'name' => 'phone', 'type' => 'tel', 'placeholder' => '7777-7777', 'autocomplete' => 'tel', 'required' => false, 'max' => 30],
    ])
    @foreach ($fields as $field)
        <div class="form-group">
            <label class="form-label" for="address_{{ $field['name'] }}">{{ $field['label'] }}@if ($field['required']) <span class="form-label__required" aria-hidden="true">*</span>@endif</label>
            <input type="{{ $field['type'] }}" id="address_{{ $field['name'] }}" name="{{ $field['name'] }}" value="{{ old($field['name'], $address->{$field['name']}) }}" placeholder="{{ $field['placeholder'] }}" maxlength="{{ $field['max'] }}" autocomplete="{{ $field['autocomplete'] }}" class="form-input @error($field['name']) form-input--error @enderror" @if ($field['required']) required @endif @if ($field['type'] === 'tel') inputmode="tel" @endif @error($field['name']) aria-invalid="true" aria-describedby="{{ $field['name'] }}-error" @enderror>
            @error($field['name'])<span class="form-error" id="{{ $field['name'] }}-error">{{ $message }}</span>@enderror
        </div>
    @endforeach
    <div class="form-group"><label class="form-label" for="address_country">País <span class="form-label__required" aria-hidden="true">*</span></label><input type="text" id="address_country" name="country" value="{{ old('country', $address->country ?: 'SV') }}" placeholder="SV" maxlength="2" required autocomplete="country" class="form-input @error('country') form-input--error @enderror">@error('country')<span class="form-error" id="country-error">{{ $message }}</span>@enderror</div>
    <div class="form-group"><label class="form-label" for="address_municipality">Municipio de entrega</label>@include('components.municipality-select', ['departments' => $departments, 'name' => 'sv_municipality_id', 'id' => 'address_municipality', 'selected' => old('sv_municipality_id', $address->sv_municipality_id)])</div>
    <label class="form-checkbox"><input type="checkbox" class="form-checkbox__input" name="is_default" value="1" @checked(old('is_default', $address->is_default))> Usar como dirección predeterminada</label>
    <button type="submit" class="btn btn--primary admin-form__submit">Guardar dirección</button>
</form>
