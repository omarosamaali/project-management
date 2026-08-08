import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const cfg = (typeof window !== 'undefined' && window.__REVERB__) ? window.__REVERB__ : {};
const key = cfg.key || import.meta.env.VITE_REVERB_APP_KEY;
const scheme = cfg.scheme || import.meta.env.VITE_REVERB_SCHEME || 'http';
const configuredHost = cfg.host || import.meta.env.VITE_REVERB_HOST || 'localhost';
const wsPort = Number(cfg.port || import.meta.env.VITE_REVERB_PORT || 8080);

if (!key) {
    // Broadcasting disabled / not configured — lecture chat falls back to polling.
    window.Echo = undefined;
} else {
    const pageHost = (typeof window !== 'undefined' && window.location?.hostname)
        ? window.location.hostname
        : configuredHost;

    // Prefer the page hostname on loopback/LAN, or whenever REVERB_HOST is localhost
    // (typical .env), so LAN clients and hosts-file domains reach this machine's Reverb.
    const isLoopbackOrLan = /^(localhost|127\.0\.0\.1|\[::1\]|192\.168\.\d{1,3}\.\d{1,3}|10\.\d{1,3}\.\d{1,3}\.\d{1,3}|172\.(1[6-9]|2\d|3[01])\.\d{1,3}\.\d{1,3})$/i
        .test(pageHost);
    const hostIsLocal = /^(localhost|127\.0\.0\.1)$/i.test(String(configuredHost || ''));
    const wsHost = (isLoopbackOrLan || hostIsLocal) ? pageHost : configuredHost;

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key,
        wsHost,
        wsPort,
        wssPort: wsPort,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
    });
}
