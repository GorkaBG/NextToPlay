@props(['type'=>"text", 'label'=>"", 'name', 'value'=>"", 'placeholder'=>""])

@if( $type != "hidden")
<label for="{{ $name }}" class="block text-lg font-semibold mb-2">
    {{ $label }}
</label>
@endif
<input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" placeholder="{{ (( ! empty($placeholder))? $placeholder:$label) }}" value="{{ $value }}" {{ $attributes(['class' => "shadow appearance-none rounded py-3 px-5 text-xl leading-tight bg-primary focus:bg-primary/50 border border-primary min-h-13 focus:outline-none focus:shadow-outline" ]) }}>
