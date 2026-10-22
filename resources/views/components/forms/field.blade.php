@props(['type'=>"text", 'label'=>"", 'name', 'value'=>""])

@if( $type != "hidden")
<label for="{{ $name }}" class="block text-lg font-semibold mb-2">
    {{ $label }}
</label>
@endif
<input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="{{ $label }}" value="{{ $value }}" {{ $attributes(['class' => "shadow appearance-none border rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" ]) }}>
