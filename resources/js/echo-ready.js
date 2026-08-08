/**
 * Wait until Laravel Echo is available (Vite module may load after inline Blade scripts).
 */
window.whenEchoReady = function whenEchoReady(timeoutMs = 8000) {
    if (window.Echo) {
        return Promise.resolve(window.Echo);
    }

    return new Promise((resolve, reject) => {
        const started = Date.now();
        const timer = setInterval(() => {
            if (window.Echo) {
                clearInterval(timer);
                resolve(window.Echo);
                return;
            }
            if (Date.now() - started >= timeoutMs) {
                clearInterval(timer);
                reject(new Error('Echo not ready'));
            }
        }, 50);
    });
};
