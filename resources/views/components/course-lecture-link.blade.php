<a href="{{ $href }}"
    @if($newTab) target="_blank" rel="noopener noreferrer" @endif
    {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot->isEmpty() ? $label : $slot }}
</a>
