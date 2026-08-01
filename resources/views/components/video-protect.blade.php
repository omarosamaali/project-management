{{-- Soft anti-download deterrents for HTML5 / Plyr players (not DRM). --}}
<style>
    .ac-video-protect,
    .ac-video-protect video,
    .ac-video-protect .plyr,
    .ac-video-protect .plyr__video-wrapper {
        -webkit-user-select: none !important;
        user-select: none !important;
        -webkit-touch-callout: none !important;
    }
    .ac-video-protect video::-webkit-media-controls-download-button {
        display: none !important;
    }
    .ac-video-protect video::-internal-media-controls-download-button {
        display: none !important;
    }
</style>
<script>
(function () {
    const roots = document.querySelectorAll('[data-ac-video-protect]');
    if (!roots.length) return;

    const block = (e) => {
        e.preventDefault();
        e.stopPropagation();
        return false;
    };

    roots.forEach((root) => {
        root.classList.add('ac-video-protect');
        root.addEventListener('contextmenu', block, true);
        root.addEventListener('dragstart', block, true);

        root.querySelectorAll('video').forEach((video) => {
            video.setAttribute('controlsList', 'nodownload noplaybackrate');
            video.setAttribute('disablePictureInPicture', 'true');
            video.setAttribute('draggable', 'false');
            video.addEventListener('contextmenu', block, true);
            video.addEventListener('dragstart', block, true);
        });
    });

    // Block common save / open-as shortcuts while focus is inside a protected player.
    document.addEventListener('keydown', (e) => {
        const target = e.target;
        const inPlayer = target && target.closest && target.closest('[data-ac-video-protect]');
        if (!inPlayer) return;
        const key = (e.key || '').toLowerCase();
        const cmd = e.ctrlKey || e.metaKey;
        if (cmd && (key === 's' || key === 'u' || key === 'p')) {
            e.preventDefault();
        }
    }, true);
})();
</script>
