<style>
    .drag-image-dropzone.is-dragover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isAllowedImage = (file) => {
            if (!file) return false;
            if (file.type && file.type.startsWith('image/')) return true;
            return /\.(jpe?g|png|webp|gif|svg)$/i.test(file.name || '');
        };

        const applyFile = (zone, file) => {
            if (!isAllowedImage(file)) return;

            const field = zone.closest('.drag-image-field');
            const input = zone.querySelector('.drag-image-input');
            const preview = field?.querySelector('.drag-image-preview');
            const filename = zone.querySelector('.drag-image-filename');
            const removeWrap = field?.querySelector('.drag-image-remove-wrap');

            if (input) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                if (preview) {
                    preview.src = event.target.result;
                    preview.classList.remove('hidden');
                    preview.classList.toggle('object-contain', /\.svg$/i.test(file.name || ''));
                    preview.classList.toggle('object-cover', !/\.svg$/i.test(file.name || ''));
                }
            };
            reader.readAsDataURL(file);

            if (filename) {
                filename.textContent = file.name;
                filename.classList.remove('hidden');
            }

            if (removeWrap) {
                const checkbox = removeWrap.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = false;
            }
        };

        document.querySelectorAll('.drag-image-dropzone').forEach((zone) => {
            const input = zone.querySelector('.drag-image-input');
            if (input) {
                input.addEventListener('change', (e) => {
                    const file = e.target.files?.[0];
                    if (file) applyFile(zone, file);
                });
            }

            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('is-dragover');
            });

            zone.addEventListener('dragleave', (e) => {
                if (!zone.contains(e.relatedTarget)) {
                    zone.classList.remove('is-dragover');
                }
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('is-dragover');
                const file = e.dataTransfer?.files?.[0];
                if (file) applyFile(zone, file);
            });
        });
    });
</script>
