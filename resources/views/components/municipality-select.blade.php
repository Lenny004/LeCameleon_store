{{--
  Grouped municipality dropdown for SV logistics (departments → municipalities).
  @param $departments — collection of SvDepartment with municipalities relation loaded
--}}
@props([
    'departments',
    'name' => 'destination_municipality_id',
    'id' => 'destination_municipality_id',
    'required' => false,
    'selected' => null,
])

@php($errorId = str_replace(['[', ']', '.'], '_', $name).'-error')
<select
    name="{{ $name }}"
    id="{{ $id }}"
    class="form-select @if ($errors->has($name)) form-select--error @endif {{ $attributes->get('class') }}"
    @if ($required) required @endif
    @if ($errors->has($name)) aria-invalid="true" aria-describedby="{{ $errorId }}" @endif
    {{ $attributes->except('class') }}
>
    <option value="">Selecciona un municipio…</option>
    @foreach ($departments as $department)
        <optgroup label="{{ $department->name }}">
            @foreach ($department->municipalities as $municipality)
                <option
                    value="{{ $municipality->id }}"
                    @selected((string) old($name, $selected) === (string) $municipality->id)
                >
                    {{ $municipality->name }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
</select>
