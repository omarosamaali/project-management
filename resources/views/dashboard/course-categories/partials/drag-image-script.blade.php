<style>
    .drag-image-dropzone.is-dragover {
        border-color: #3b82f6;
        background: #eff6ff;
    }
    .drag-image-dropzone.is-has-file {
        border-color: #34d399;
        background: rgba(16, 185, 129, .08);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isAllowedImage = (file) => {
            if (!file) return false;
            if (file.type && file.type.startsWith('image/')) return true;
            return /\.(jpe?g|png|webp|gif|svg)$/i.test(file.name || '');
        };

        const setStatus = (field, mode) => {
            const status = field?.querySelector('.drag-image-status');
            const text = status?.querySelector('.drag-image-status-text');
            const icon = status?.querySelector('.drag-image-status-icon');
            if (!status || !text) return;

            status.classList.remove(
                'bg-slate-100', 'text-slate-500', 'border-slate-200',
                'bg-emerald-50', 'text-emerald-700', 'border-emerald-200',
                'bg-blue-50', 'text-blue-700', 'border-blue-200'
            );

            if (mode === 'new') {
                text.textContent = status.dataset.newLabel || 'تم اختيار صورة جديدة';
                status.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-200');
                if (icon) icon.className = 'drag-image-status-icon fas fa-file-image';
            } else if (mode === 'existing') {
                text.textContent = status.dataset.existingLabel || 'صورة محفوظة';
                status.classList.add('bg-emerald-50', 'text-emerald-700', 'border-emerald-200');
                if (icon) icon.className = 'drag-image-status-icon fas fa-check-circle';
            } else {
                text.textContent = status.dataset.emptyLabel || 'لم تُرفع صورة';
                status.classList.add('bg-slate-100', 'text-slate-500', 'border-slate-200');
                if (icon) icon.className = 'drag-image-status-icon fas fa-image';
            }
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

            zone.classList.add('is-has-file');
            setStatus(field, 'new');

            if (removeWrap) {
                const checkbox = removeWrap.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = false;
                removeWrap.classList.remove('hidden');
            }
        };

        document.querySelectorAll('.drag-image-dropzone').forEach((zone) => {
            const field = zone.closest('.drag-image-field');
            const input = zone.querySelector('.drag-image-input');
            const hasExisting = field?.dataset.hasExisting === '1';

            if (hasExisting) {
                zone.classList.add('is-has-file');
            }

            if (input) {
                input.addEventListener('change', (e) => {
                    const file = e.target.files?.[0];
                    if (file) {
                        applyFile(zone, file);
                    } else if (hasExisting) {
                        setStatus(field, 'existing');
                        zone.classList.add('is-has-file');
                    } else {
                        setStatus(field, 'empty');
                        zone.classList.remove('is-has-file');
                    }
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
