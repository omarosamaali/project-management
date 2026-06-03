@once
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-project-chat-form]').forEach(function(form) {
            const container = form.closest('.flex.flex-col')?.querySelector('[data-messages-container]');
            if (!container) return;

            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const input = form.querySelector('[name="message"]');
                const text = (input?.value || '').trim();
                if (!text) return;

                const btn = form.querySelector('button[type="submit"]');
                if (btn) btn.disabled = true;

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]')?.value || '',
                        },
                        body: new FormData(form),
                    });

                    if (!res.ok) throw new Error('send failed');
                    const data = await res.json();
                    if (data.message) {
                        appendChatMessage(container, data.message);
                        if (input) input.value = '';
                    }
                } catch (err) {
                    form.removeAttribute('data-project-chat-form');
                    form.submit();
                    return;
                } finally {
                    if (btn) btn.disabled = false;
                }
            });

            container.scrollTop = container.scrollHeight;
        });
    });

    function appendChatMessage(container, m) {
        const empty = container.querySelector('[data-chat-empty]');
        if (empty) empty.remove();

        const isMine = !!m.is_mine;
        const row = document.createElement('div');
        row.className = 'flex ' + (isMine ? 'justify-start flex-row-reverse' : 'justify-start') + ' items-end gap-2';

        const avatar = document.createElement('div');
        avatar.className =
            'w-8 h-8 rounded-full bg-gray-300 flex-shrink-0 flex items-center justify-center text-[10px] font-bold text-white shadow-sm overflow-hidden';
        if (m.user_image) {
            const img = document.createElement('img');
            img.src = m.user_image;
            img.alt = '';
            avatar.appendChild(img);
        } else {
            avatar.textContent = m.user_initial || 'U';
        }

        const body = document.createElement('div');
        body.className = 'max-w-[70%] space-y-1';

        const meta = document.createElement('div');
        meta.className = 'flex items-center gap-2 ' + (isMine ? 'flex-row-reverse' : '');

        const name = document.createElement('span');
        name.className = 'text-[11px] font-bold text-gray-600 dark:text-gray-400';
        name.textContent = m.user_name || 'مستخدم';

        const time = document.createElement('span');
        time.className = 'text-[9px] text-gray-400';
        time.textContent = m.created_at_human || '';

        meta.appendChild(name);
        meta.appendChild(time);

        const bubble = document.createElement('div');
        bubble.className = 'p-3 rounded-2xl text-sm shadow-sm ' + (isMine ?
            'bg-blue-600 text-white rounded-br-none' :
            'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 border dark:border-gray-700 rounded-bl-none'
        );
        bubble.textContent = m.message || '';

        body.appendChild(meta);
        body.appendChild(bubble);
        row.appendChild(avatar);
        row.appendChild(body);
        container.appendChild(row);
        container.scrollTop = container.scrollHeight;
    }
</script>
@endonce
