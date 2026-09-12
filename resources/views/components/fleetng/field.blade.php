@props(['name', 'label', 'type' => 'text', 'value' => '', 'required' => false])
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}@if($required) <span aria-hidden="true">*</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}"
           value="{{ $type === 'password' ? '' : old($name, $value) }}"
           {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }}
           @if($required) required @endif
           @if($errors->has($name)) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif>
    @error($name)<div id="{{ $name }}-error" class="invalid-feedback">{{ $message }}</div>@enderror
</div>
