<style>
    .chat-mod-modal {
        position: fixed;
        inset: 0;
        z-index: 80;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .chat-mod-modal.is-open { display: flex; }
    .chat-mod-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
    }
    .chat-mod-modal__panel {
        position: relative;
        width: min(100%, 24rem);
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.2);
        padding: 1.15rem 1.2rem 1rem;
        direction: rtl;
    }
    .chat-mod-modal__title {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }
    .chat-mod-modal__body {
        margin: 0.65rem 0 1rem;
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.6;
    }
    .chat-mod-modal__name {
        font-weight: 700;
        color: #0f172a;
    }
    .chat-mod-modal__actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-start;
        flex-wrap: wrap;
    }
    .chat-mod-modal__btn {
        border: 0;
        border-radius: 0.65rem;
        padding: 0.55rem 0.95rem;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
    }
    .chat-mod-modal__btn:disabled { opacity: 0.6; cursor: wait; }
    .chat-mod-modal__btn--ghost {
        background: #f1f5f9;
        color: #334155;
    }
    .chat-mod-modal__btn--danger {
        background: #dc2626;
        color: #fff;
    }
    .chat-mod-modal__btn--success {
        background: #059669;
        color: #fff;
    }
</style>

<div id="chatModModal" class="chat-mod-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="chat-mod-modal__backdrop" data-chat-mod-dismiss></div>
    <div class="chat-mod-modal__panel">
        <h3 class="chat-mod-modal__title" id="chatModModalTitle">إدارة المتدرب</h3>
        <p class="chat-mod-modal__body" id="chatModModalBody"></p>
        <div class="chat-mod-modal__actions">
            <button type="button" class="chat-mod-modal__btn chat-mod-modal__btn--ghost" data-chat-mod-dismiss>إلغاء</button>
            <button type="button" class="chat-mod-modal__btn" id="chatModModalConfirm">تأكيد</button>
        </div>
    </div>
</div>

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
    const courseId = root.dataset.courseId;
    const authUserId = parseInt(root.dataset.authUserId || '0', 10);

    const box = document.getElementById('chatMessages');
    const empty = document.getElementById('chatEmpty');
    const form = document.getElementById('chatForm');
    const input = document.getElementById('chatInput');
    const statusEl = document.getElementById('chatStatus');
    const lockToggle = document.getElementById('chatLockToggle');
    const lockLabel = document.getElementById('chatLockLabel');
    const lockedNotice = document.getElementById('chatLockedNotice');

    const modal = document.getElementById('chatModModal');
    const modalTitle = document.getElementById('chatModModalTitle');
    const modalBody = document.getElementById('chatModModalBody');
    const modalConfirm = document.getElementById('chatModModalConfirm');

    let lastId = 0;
    let polling = false;
    const seen = new Set();
    const blockedUsers = new Set();
    let pendingModAction = null;

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

    function openModModal({ title, html, confirmLabel, confirmClass, action }) {
        if (!modal) return Promise.resolve(false);
        return new Promise((resolve) => {
            pendingModAction = { resolve, action };
            modalTitle.textContent = title;
            modalBody.innerHTML = html;
            modalConfirm.textContent = confirmLabel;
            modalConfirm.className = 'chat-mod-modal__btn ' + confirmClass;
            modalConfirm.disabled = false;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });
    }

    function closeModModal(result) {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        const pending = pendingModAction;
        pendingModAction = null;
        if (pending && typeof pending.resolve === 'function') {
            pending.resolve(result);
        }
    }

    if (modal) {
        modal.querySelectorAll('[data-chat-mod-dismiss]').forEach((el) => {
            el.addEventListener('click', () => closeModModal(false));
        });
        modalConfirm.addEventListener('click', async () => {
            const pending = pendingModAction;
            if (!pending || !pending.action) {
                closeModModal(false);
                return;
            }
            modalConfirm.disabled = true;
            try {
                const ok = await pending.action();
                closeModModal(!!ok);
            } catch (err) {
                modalConfirm.disabled = false;
                alert('فشلت العملية');
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModModal(false);
            }
        });
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
        if (lockToggle) lockToggle.checked = !chatLocked;
        if (lockLabel) {
            lockLabel.textContent = chatLocked ? 'المتدربون ممنوعون' : 'السماح للمتدربين';
        }
    }

    function syncBlockedButtons(msgEl, userId) {
        if (!canModerate || !msgEl) return;
        const uid = parseInt(userId, 10);
        const blockBtn = msgEl.querySelector('[data-action="block"]');
        const unblockBtn = msgEl.querySelector('[data-action="unblock"]');
        const isUserBlocked = blockedUsers.has(uid);
        if (blockBtn) blockBtn.style.display = isUserBlocked ? 'none' : '';
        if (unblockBtn) unblockBtn.style.display = isUserBlocked ? '' : 'none';
    }

    function applyHiddenUi(msgEl, m) {
        if (!msgEl) return;
        msgEl.classList.toggle('hidden-msg', !!m.is_hidden);

        let authorLabel = msgEl.querySelector('[data-hidden-author-label]');
        const showAuthorLabel = !!m.is_hidden && !!m.is_mine && !canModerate;
        if (showAuthorLabel) {
            if (!authorLabel) {
                authorLabel = document.createElement('span');
                authorLabel.className = 'hidden-author-label';
                authorLabel.dataset.hiddenAuthorLabel = '1';
                authorLabel.innerHTML = '<i class="fas fa-eye-slash"></i> أخفى المحاضر هذه الرسالة';
                const meta = msgEl.querySelector('.meta');
                if (meta) meta.appendChild(authorLabel);
            }
            authorLabel.style.display = '';
        } else if (authorLabel) {
            authorLabel.style.display = 'none';
        }

        const modBadge = msgEl.querySelector('[data-hidden-badge]');
        if (modBadge) {
            modBadge.style.display = (canModerate && m.is_hidden) ? '' : 'none';
        }

        const hideBtn = msgEl.querySelector('[data-action="hide"]');
        const unhideBtn = msgEl.querySelector('[data-action="unhide"]');
        if (hideBtn) hideBtn.style.display = m.is_hidden ? 'none' : '';
        if (unhideBtn) unhideBtn.style.display = m.is_hidden ? '' : 'none';
    }

    function normalizeMessage(m) {
        if (!m || typeof m !== 'object') return m;
        m.is_mine = parseInt(m.user_id, 10) === authUserId;
        return m;
    }

    function handleRealtimeMessage(m) {
        m = normalizeMessage(m);
        if (!m) return;
        if (m.is_hidden && !canModerate && !m.is_mine) {
            removeMessage(m.id);
            return;
        }
        upsertMessage(m);
    }

    function upsertMessage(m) {
        m = normalizeMessage(m);
        const existing = box.querySelector('[data-msg-id="' + m.id + '"]');
        if (existing) {
            applyHiddenUi(existing, m);
            syncBlockedButtons(existing, m.user_id);
            return;
        }
        renderMessage(m);
    }

    function removeMessage(id) {
        const el = box.querySelector('[data-msg-id="' + id + '"]');
        if (el) el.remove();
        seen.delete(Number(id));
        if (box && !box.querySelector('.lecture-msg') && empty) {
            empty.style.display = '';
        }
    }

    function renderMessage(m) {
        m = normalizeMessage(m);
        if (seen.has(m.id)) {
            upsertMessage(m);
            return;
        }
        seen.add(m.id);
        if (m.id > lastId) lastId = m.id;
        if (empty) empty.style.display = 'none';

        const div = document.createElement('div');
        div.className = 'lecture-msg' + (m.is_mine ? ' mine' : '') + (m.is_hidden ? ' hidden-msg' : '');
        div.dataset.msgId = m.id;
        div.dataset.userId = m.user_id;
        div.dataset.userName = m.user_name || '';

        const color = m.user_color || '#64748b';
        const initials = esc(m.user_initials || 'م');
        const avatarInner = m.user_avatar
            ? `<img src="${esc(m.user_avatar)}" alt="" onerror="this.replaceWith(document.createTextNode('${initials}'))">`
            : initials;

        let actions = '';
        if (canModerate) {
            const uid = parseInt(m.user_id, 10);
            const isUserBlocked = blockedUsers.has(uid);
            actions = `
                <button type="button" data-action="hide" class="text-amber-600 hover:underline" style="display:${m.is_hidden ? 'none' : ''}">إخفاء</button>
                <button type="button" data-action="unhide" class="text-green-600 hover:underline" style="display:${m.is_hidden ? '' : 'none'}">إظهار</button>
                ${!m.is_mine ? `
                    <button type="button" data-action="block" class="text-red-600 hover:underline" style="display:${isUserBlocked ? 'none' : ''}">حظر</button>
                    <button type="button" data-action="unblock" class="text-emerald-700 hover:underline" style="display:${isUserBlocked ? '' : 'none'}">إلغاء الحظر</button>
                ` : ''}
            `;
        }

        const authorLabel = (m.is_hidden && m.is_mine && !canModerate)
            ? `<span class="hidden-author-label" data-hidden-author-label="1"><i class="fas fa-eye-slash"></i> أخفى المحاضر هذه الرسالة</span>`
            : '';

        div.innerHTML = `
            <div class="avatar" style="background:${esc(color)}">${avatarInner}</div>
            <div class="msg-body">
                <div class="bubble" style="background:${colorWithAlpha(color, 0.18)};border-color:${colorWithAlpha(color, 0.35)};">${esc(m.body)}</div>
                <div class="meta">
                    <span>${esc(m.user_name)}</span>
                    <span>${esc(m.created_at || '')}</span>
                    <span data-hidden-badge style="display:${(canModerate && m.is_hidden) ? '' : 'none'};color:#b45309">مخفي</span>
                    ${authorLabel}
                    ${actions}
                </div>
            </div>
        `;
        box.appendChild(div);
        if (live) box.scrollTop = box.scrollHeight;
    }

    function syncBlockedUsers(ids) {
        blockedUsers.clear();
        (ids || []).forEach((id) => blockedUsers.add(parseInt(id, 10)));
        if (!canModerate || !box) return;
        box.querySelectorAll('.lecture-msg').forEach((el) => {
            syncBlockedButtons(el, el.dataset.userId);
        });
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

            if (canModerate) {
                syncBlockedUsers(data.blocked_user_ids || []);
            }

            (data.purge_ids || []).forEach((id) => removeMessage(id));
            (data.messages || []).forEach((m) => renderMessage(normalizeMessage(m)));
            (data.own_hidden || []).forEach((m) => upsertMessage(normalizeMessage(m)));

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
            // Checked = allow trainees (not locked).
            const allowed = !!lockToggle.checked;
            const locked = !allowed;
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
                    lockToggle.checked = !allowed;
                    alert(data.message || 'تعذر تحديث حالة النقاش');
                    return;
                }
                chatLocked = !!data.chat_locked;
                updateComposerVisibility();
            } catch (err) {
                lockToggle.checked = !allowed;
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
        const userName = msgEl.dataset.userName || 'المتدرب';
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
                    upsertMessage(data.message);
                }
            }

            if (action === 'block') {
                await openModModal({
                    title: 'حظر من النقاش',
                    html: `هل تريد حظر <span class="chat-mod-modal__name">${esc(userName)}</span> من المشاركة في النقاش؟ لن يتمكن من إرسال رسائل جديدة.`,
                    confirmLabel: 'تأكيد الحظر',
                    confirmClass: 'chat-mod-modal__btn--danger',
                    action: async () => {
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
                            return false;
                        }
                        blockedUsers.add(parseInt(userId, 10));
                        box.querySelectorAll('.lecture-msg[data-user-id="' + userId + '"]').forEach((el) => {
                            syncBlockedButtons(el, userId);
                        });
                        return true;
                    }
                });
            }

            if (action === 'unblock') {
                await openModModal({
                    title: 'إلغاء الحظر',
                    html: `هل تريد السماح لـ <span class="chat-mod-modal__name">${esc(userName)}</span> بالمشاركة في النقاش مرة أخرى؟`,
                    confirmLabel: 'إلغاء الحظر',
                    confirmClass: 'chat-mod-modal__btn--success',
                    action: async () => {
                        const res = await fetch(unblockBase + '/' + userId, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        if (!res.ok) {
                            const data = await res.json().catch(() => ({}));
                            alert(data.message || 'تعذر إلغاء الحظر');
                            return false;
                        }
                        blockedUsers.delete(parseInt(userId, 10));
                        box.querySelectorAll('.lecture-msg[data-user-id="' + userId + '"]').forEach((el) => {
                            syncBlockedButtons(el, userId);
                        });
                        return true;
                    }
                });
            }
        } catch (err) {
            alert('فشلت العملية');
        }
    });

    updateComposerVisibility();
    // Initial history load only — realtime updates come from Echo.
    poll();

    let pollTimer = null;
    const stopPolling = () => {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    };
    const startFallbackPolling = (ms) => {
        stopPolling();
        pollTimer = setInterval(poll, ms);
    };

    const subscribeChat = (Echo) => {
        if (!courseId) return;
        stopPolling();
        Echo.private('course.' + courseId + '.chat')
            .listen('.chat.message.created', (e) => {
                if (e && e.message) handleRealtimeMessage(e.message);
            })
            .listen('.chat.message.updated', (e) => {
                if (e && e.message) handleRealtimeMessage(e.message);
            })
            .listen('.chat.user.moderation', (e) => {
                if (!e) return;
                const uid = parseInt(e.user_id, 10);
                if (e.blocked) blockedUsers.add(uid);
                else blockedUsers.delete(uid);
                box.querySelectorAll('.lecture-msg[data-user-id="' + uid + '"]').forEach((el) => {
                    syncBlockedButtons(el, uid);
                });
                if (uid === authUserId) {
                    isBlocked = !!e.blocked;
                    updateComposerVisibility();
                }
            })
            .listen('.chat.lock.toggled', (e) => {
                if (typeof e?.chat_locked === 'boolean') {
                    chatLocked = e.chat_locked;
                    updateComposerVisibility();
                }
            });

        // Refresh once when tab becomes visible again (missed events while backgrounded).
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) poll();
        });
    };

    if (!live) {
        // Archive page: one-shot load is enough.
    } else {
        const wait = typeof window.whenEchoReady === 'function'
            ? window.whenEchoReady(8000)
            : (window.Echo
                ? Promise.resolve(window.Echo)
                : new Promise((resolve, reject) => {
                    const started = Date.now();
                    const t = setInterval(() => {
                        if (window.Echo) {
                            clearInterval(t);
                            resolve(window.Echo);
                        } else if (Date.now() - started > 8000) {
                            clearInterval(t);
                            reject(new Error('Echo timeout'));
                        }
                    }, 50);
                }));

        wait.then(subscribeChat).catch(() => {
            // Websocket unavailable — fall back to slow polling, not 2.5s.
            startFallbackPolling(15000);
        });
    }
})();
</script>
