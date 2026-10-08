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

<select
    name="{{ $name }}"
    id="{{ $id }}"
    class="form-input {{ $attributes->get('class') }}"
    @if ($required) required @endif
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
