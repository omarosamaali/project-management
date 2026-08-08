import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const scheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
const configuredHost = import.meta.env.VITE_REVERB_HOST || 'localhost';
// Prefer the page hostname so LAN clients (e.g. 192.168.x.x) hit the same machine.
const wsHost = (typeof window !== 'undefined' && window.location?.hostname)
    ? window.location.hostname
    : configuredHost;
const wsPort = Number(import.meta.env.VITE_REVERB_PORT || 8080);

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost,
    wsPort,
    wssPort: wsPort,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
});
