{{-- Runtime Reverb settings (avoids stale Vite-baked keys/hosts). --}}
@php
    $reverbEnabled = config('broadcasting.default') === 'reverb'
        && filled(config('broadcasting.connections.reverb.key'));
@endphp
@if($reverbEnabled)
<script>
    window.__REVERB__ = {
        key: @json(config('broadcasting.connections.reverb.key')),
        host: @json(config('broadcasting.connections.reverb.options.host') ?: request()->getHost()),
        port: @json((int) (config('broadcasting.connections.reverb.options.port') ?: 8080)),
        scheme: @json(config('broadcasting.connections.reverb.options.scheme') ?: 'http')
    };
</script>
@endif
