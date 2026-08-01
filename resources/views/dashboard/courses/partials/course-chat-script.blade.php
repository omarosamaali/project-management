<script>
(function () {
    const root = document.getElementById('lectureChat') || document.getElementById('archiveChat');
    if (!root) return;

    const pollUrl = root.dataset.pollUrl;
    const storeUrl = root.dataset.storeUrl;
    const hideBase = root.dataset.hideUrl;
    const blockUrl = root.dataset.blockUrl;
    const unblockBase = root.dataset.unblockUrl;
    const lockUrl = root.dataset.lockUrl;
    const csrf = root.dataset.csrf;
    const canModerate = root.dataset.canModerate === '1';
    const canChat = root.dataset.canChat === '1';
    let isBlocked = root.dataset.isBlocked === '1';
    let chatLocked = root.dataset.chatLocked === '1';
    const live = root.dataset.live !== '0';

    const box = document.getElementById('chatMessages');
    const empty = document.getElementById('chatEmpty');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const statusEl = document.getElementById('chatStatus');
    const lockToggle = document.getElementById('chatLockToggle');
    const lockLabel = document.getElementById('chatLockLabel');
    const lockedNotice = document.getElementById('chatLockedNotice');

    let lastId = 0;
    let polling = false;
    const seen = new Set();

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function colorWithAlpha(hex, alpha) {
        const h = String(hex || '#64748b').replace('#', '');
        if (h.length !== 6) return `rgba(100,116,139,${alpha})`;
        const r = parseInt(h.slice(0, 2), 16);
        const g = parseInt(h.slice(2, 4), 16);
        const b = parseInt(h.slice(4, 6), 16);
        return `rgba(${r},${g},${b},${alpha})`;
    }

    function updateComposerVisibility() {
        const traineeLocked = !canModerate && chatLocked;
        if (form) {
            if (isBlocked && !canModerate) {
                form.style.display = 'none';
            } else if (traineeLocked) {
                form.style.display = 'none';
            } else if (canChat) {
                form.style.display = 'flex';
            }
        }
        if (lockedNotice) {
            lockedNotice.style.display = traineeLocked && !isBlocked ? 'block' : 'none';
        }
        if (lockToggle) lockToggle.checked = !!chatLocked;
        if (lockLabel) {
            lockLabel.textContent = chatLocked ? 'المتدربون ممنوعون' : 'السماح للمتدربين';
        }
    }

    function renderMessage(m) {
        if (seen.has(m.id)) {
            // update hidden state if moderator
            const existing = box.querySelector('[data-msg-id="' + m.id + '"]');
            if (existing && canModerate) {
                existing.classList.toggle('hidden-msg', !!m.is_hidden);
                const badge = existing.querySelector('[data-hidden-badge]');
                if (badge) badge.style.display = m.is_hidden ? '' : 'none';
            }
            return;
        }
        seen.add(m.id);
        if (m.id > lastId) lastId = m.id;
        if (empty) empty.style.display = 'none';

        const div = document.createElement('div');
        div.className = 'lecture-msg' + (m.is_mine ? ' mine' : '') + (m.is_hidden ? ' hidden-msg' : '');
        div.dataset.msgId = m.id;
        div.dataset.userId = m.user_id;

        const color = m.user_color || '#64748b';
        const initials = esc(m.user_initials || 'م');
        const avatarInner = m.user_avatar
            ? `<img src="${esc(m.user_avatar)}" alt="" onerror="this.replaceWith(document.createTextNode('${initials}'))">`
            : initials;

        let actions = '';
        if (canModerate) {
            actions = `
                <button type="button" data-action="hide" class="text-amber-600 hover:underline" style="display:${m.is_hidden ? 'none' : ''}">إخفاء</button>
                <button type="button" data-action="unhide" class="text-green-600 hover:underline" style="display:${m.is_hidden ? '' : 'none'}">إظهار</button>
                ${!m.is_mine ? `<button type="button" data-action="block" class="text-red-600 hover:underline">حظر</button>` : ''}
            `;
        }

        div.innerHTML = `
            <div class="avatar" style="background:${esc(color)}">${avatarInner}</div>
            <div class="msg-body">
                <div class="bubble" style="background:${colorWithAlpha(color, 0.18)};border-color:${colorWithAlpha(color, 0.35)};">${esc(m.body)}</div>
                <div class="meta">
                    <span>${esc(m.user_name)}</span>
                    <span>${esc(m.created_at || '')}</span>
                    <span data-hidden-badge style="display:${m.is_hidden ? '' : 'none'};color:#b45309">مخفي</span>
                    ${actions}
                </div>
            </div>
        `;
        box.appendChild(div);
        if (live) box.scrollTop = box.scrollHeight;
    }

    async function poll() {
        if (polling) return;
        polling = true;
        try {
            const url = pollUrl + (lastId ? ('?after_id=' + lastId) : '');
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            });
            if (res.status === 401 || res.status === 419) {
                if (window.showSessionExpiredModal) window.showSessionExpiredModal();
                return;
            }
            if (!res.ok) throw new Error('poll failed');
            const data = await res.json();
            (data.messages || []).forEach(renderMessage);
            if (typeof data.is_blocked === 'boolean') {
                isBlocked = data.is_blocked;
            }
            if (typeof data.chat_locked === 'boolean') {
                chatLocked = data.chat_locked;
            }
            updateComposerVisibility();
            if (statusEl) {
                statusEl.textContent = live ? 'متصل' : 'جاهز';
                statusEl.className = 'text-[11px] text-green-600';
            }
        } catch (e) {
            if (statusEl) {
                statusEl.textContent = 'إعادة الاتصال...';
                statusEl.className = 'text-[11px] text-amber-600';
            }
        } finally {
            polling = false;
        }
    }

    if (lockToggle && lockUrl && canModerate) {
        lockToggle.addEventListener('change', async function () {
            const locked = !!lockToggle.checked;
            lockToggle.disabled = true;
            try {
                const res = await fetch(lockUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ locked })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    lockToggle.checked = !locked;
                    alert(data.message || 'تعذر تحديث حالة النقاش');
                    return;
                }
                chatLocked = !!data.chat_locked;
                updateComposerVisibility();
            } catch (err) {
                lockToggle.checked = !locked;
                alert('تعذر تحديث حالة النقاش');
            } finally {
                lockToggle.disabled = false;
            }
        });
    }

    if (form && input && canChat && !isBlocked) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!canModerate && chatLocked) {
                alert('تم إيقاف إرسال الرسائل من قبل المحاضر.');
                updateComposerVisibility();
                return;
            }
            const body = input.value.trim();
            if (!body) return;
            input.disabled = true;
            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ body })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    if (data.message && data.message.indexOf('إيقاف') !== -1) {
                        chatLocked = true;
                        updateComposerVisibility();
                    }
                    alert(data.message || 'تعذر إرسال الرسالة');
                    return;
                }
                input.value = '';
                if (data.message) renderMessage(data.message);
            } catch (err) {
                alert('تعذر إرسال الرسالة');
            } finally {
                input.disabled = false;
                input.focus();
            }
        });
    }

    box.addEventListener('click', async function (e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn || !canModerate) return;
        const msgEl = btn.closest('.lecture-msg');
        if (!msgEl) return;
        const msgId = msgEl.dataset.msgId;
        const userId = msgEl.dataset.userId;
        const action = btn.dataset.action;

        try {
            if (action === 'hide' || action === 'unhide') {
                const res = await fetch(hideBase + '/' + msgId + '/' + action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (data.message) {
                    seen.delete(data.message.id);
                    msgEl.remove();
                    renderMessage(data.message);
                }
            }
            if (action === 'block') {
                if (!confirm('حظر هذا المتدرب من النقاش؟')) return;
                const res = await fetch(blockUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ user_id: parseInt(userId, 10) })
                });
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    alert(data.message || 'تعذر الحظر');
                    return;
                }
                alert('تم حظر المتدرب من النقاش');
            }
            if (action === 'unblock') {
                const res = await fetch(unblockBase + '/' + userId, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });
                if (res.ok) alert('تم إلغاء الحظر');
            }
        } catch (err) {
            alert('فشلت العملية');
        }
    });

    updateComposerVisibility();
    poll();
    if (live) setInterval(poll, 2500);
})();
</script>
