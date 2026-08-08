/**
 * Shared day-exam redirect listener (replaces aggressive pending-check polling).
 * Call once on authenticated academy/dashboard pages.
 */
window.setupDayExamRealtime = function setupDayExamRealtime(options = {}) {
    const checkUrl = options.checkUrl;
    const userId = options.userId;
    if (!checkUrl || !userId) return;

    const path = window.location.pathname;
    if (/\/courses\/[^/]+\/exam/.test(path)) return;

    let redirecting = false;
    let fallbackTimer = null;

    const goToExam = (redirect, courseName) => {
        if (!redirect || redirecting) return;
        redirecting = true;
        const go = () => { window.location.href = redirect; };
        if (typeof Swal === 'undefined') {
            go();
            return;
        }
        Swal.fire({
            title: 'الاختبار متاح الآن',
            text: courseName ? ('يتم تحويلك لاختبار: ' + courseName) : 'يتم تحويلك لصفحة الاختبار الآن',
            icon: 'info',
            timer: 900,
            showConfirmButton: false,
            allowOutsideClick: false,
        }).then(go);
        setTimeout(go, 1000);
    };

    const checkOnce = () => {
        if (redirecting || document.hidden) return;
        fetch(checkUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then((r) => r.json())
            .then((data) => goToExam(data.redirect, data.course_name))
            .catch(() => {});
    };

    // Catch exams already running when the page loads / tab becomes visible.
    checkOnce();
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) checkOnce();
    });

    const subscribe = (Echo) => {
        if (fallbackTimer) {
            clearInterval(fallbackTimer);
            fallbackTimer = null;
        }
        Echo.private('App.Models.User.' + userId)
            .listen('.day-exam.started', (e) => {
                goToExam(e.redirect, e.course_name);
            });
    };

    const wait = typeof window.whenEchoReady === 'function'
        ? window.whenEchoReady(8000)
        : Promise.reject(new Error('no waiter'));

    wait.then(subscribe).catch(() => {
        // No websocket — rare slow fallback only.
        if (!fallbackTimer) {
            fallbackTimer = setInterval(checkOnce, 30000);
        }
    });
};
