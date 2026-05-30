/**
 * RentWheels — app.js
 * Stack: Alpine.js (x-data directives already used in blade)
 *        Vanilla JS utilities: chat, toast, auto-dismiss, textarea auto-grow
 */
import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

/* ─── Toast System ──────────────────────────────────────────── */
window.showToast = function (message, type = 'success', duration = 3500) {
    const icons = {
        success: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
        error:   `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`,
        warning: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>`,
        info:    `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`,
    };

    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `rw-toast rw-toast--${type}`;
    toast.innerHTML = `
        <span style="color:${type === 'success' ? 'var(--success)' : type === 'error' ? 'var(--danger)' : type === 'warning' ? 'var(--warning)' : 'var(--info)'};">${icons[type] ?? icons.info}</span>
        <span class="rw-toast__msg">${message}</span>
        <button class="rw-toast__close" onclick="this.closest('.rw-toast').remove()">✕</button>
    `;
    container.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('show'));

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 320);
    }, duration);
};

/* ─── Auto-dismiss Flash Alerts ────────────────────────────── */
function initAutoDismiss() {
    document.querySelectorAll('[data-auto-dismiss]').forEach(el => {
        const delay = parseInt(el.dataset.autoDismiss) || 4000;
        setTimeout(() => {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 420);
        }, delay);
    });
}

/* ─── Auto-grow Textareas ───────────────────────────────────── */
function initAutoGrow() {
    document.querySelectorAll('textarea.chat-textarea').forEach(el => {
        const grow = () => {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 100) + 'px';
        };
        el.addEventListener('input', grow);
        grow();
    });
}

/* ─── Chat Engine ────────────────────────────────────────────── */
window.initChat = function (bookingId, currentUserId, sendUrl, csrfToken) {
    const form      = document.getElementById('chat-form');
    const input     = document.getElementById('chat-input');
    const sendBtn   = document.getElementById('chat-send-btn');
    const msgBox    = document.getElementById('chat-messages');
    const errorBox  = document.getElementById('chat-error');

    if (!form || !input || !msgBox) return;

    // Scroll to bottom on load
    msgBox.scrollTop = msgBox.scrollHeight;

    // Enter = send, Shift+Enter = newline
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
        }
    });

    // Submit
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const content = input.value.trim();
        if (!content) return;

        setLoading(true);
        if (errorBox) errorBox.style.display = 'none';

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ content }),
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Gagal mengirim pesan');
            }

            input.value = '';
            input.style.height = 'auto';
            appendMessage(data.message, true);
        } catch (err) {
            if (errorBox) {
                errorBox.textContent = err.message;
                errorBox.style.display = 'flex';
            }
        } finally {
            setLoading(false);
        }
    });

    // Realtime via Pusher — fallback polling jika Echo tidak tersedia
    let lastId = getLastMessageId();

    if (window.Echo) {
        window.Echo.private(`booking.${bookingId}`)
            .listen('.MessageSent', (e) => {
                const msg = e.message;
                if (msg.sender_id === currentUserId) return;
                appendMessage(msg, false);
                lastId = Math.max(lastId, msg.id);
            });
    } else {
        // Fallback polling 5s jika Pusher tidak terkonfigurasi
        setInterval(async () => {
            try {
                const res = await fetch(`/sewa/chat/${bookingId}/pesan-baru?after=${lastId}`, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                });
                if (!res.ok) return;
                const data = await res.json();
                if (data.messages?.length) {
                    data.messages.forEach(msg => {
                        if (msg.sender_id !== currentUserId) {
                            appendMessage(msg, false);
                        }
                        lastId = Math.max(lastId, msg.id);
                    });
                }
            } catch {}
        }, 5000);
    }

    function appendMessage(msg, isMine) {
        // Remove empty state
        document.querySelector('.chat-empty')?.remove();

        const wrapper = document.createElement('div');
        wrapper.className = `chat-bubble-wrapper ${isMine ? 'mine' : 'theirs'}`;
        wrapper.dataset.messageId = msg.id;

        const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        const checkmark = isMine
            ? `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="2.5" style="display:inline;vertical-align:middle;margin-left:2px;"><polyline points="20 6 9 17 4 12"/></svg>`
            : '';

        wrapper.innerHTML = `
            <div>
                <div class="chat-bubble ${isMine ? 'mine' : 'theirs'}">${escapeHtml(msg.content)}</div>
                <div class="chat-time" style="${isMine ? 'text-align:right;' : ''}">${time} ${checkmark}</div>
            </div>
        `;

        msgBox.appendChild(wrapper);
        msgBox.scrollTo({ top: msgBox.scrollHeight, behavior: 'smooth' });

        lastId = Math.max(lastId, msg.id);

        // Play subtle sound indicator
        playNotif();
    }

    function getLastMessageId() {
        const els = msgBox.querySelectorAll('[data-message-id]');
        let max = 0;
        els.forEach(el => max = Math.max(max, parseInt(el.dataset.messageId) || 0));
        return max;
    }


    function setLoading(loading) {
        if (sendBtn) {
            sendBtn.disabled = loading;
            sendBtn.style.opacity = loading ? '.5' : '1';
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function playNotif() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.value = 880;
            gain.gain.setValueAtTime(0, ctx.currentTime);
            gain.gain.linearRampToValueAtTime(0.08, ctx.currentTime + 0.01);
            gain.gain.linearRampToValueAtTime(0, ctx.currentTime + 0.2);
            osc.start(ctx.currentTime);
            osc.stop(ctx.currentTime + 0.2);
        } catch {}
    }
};

/* ─── Pagination custom simple links ────────────────────────── */
// Custom links handled by pagination view — no JS needed

/* ─── Date input min enforcement ───────────────────────────── */
function initDateInputs() {
    const today = new Date().toISOString().split('T')[0];
    const startInput = document.querySelector('input[name="start_date"]');
    const endInput   = document.querySelector('input[name="end_date"]');

    if (startInput && !startInput.value) startInput.min = today;
    if (startInput && endInput) {
        startInput.addEventListener('change', () => {
            if (startInput.value) {
                const next = new Date(startInput.value);
                next.setDate(next.getDate() + 1);
                endInput.min = next.toISOString().split('T')[0];
                if (endInput.value && endInput.value <= startInput.value) {
                    endInput.value = next.toISOString().split('T')[0];
                }
            }
        });
    }
}

/* ─── Image Preview (vehicle form) ─────────────────────────── */
function initImagePreview() {
    document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
        const previewId = input.dataset.preview;
        const preview = document.getElementById(previewId);
        if (!preview) return;
        input.addEventListener('change', () => {
            const file = input.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    });
}

/* ─── Confirm dialogs ───────────────────────────────────────── */
function initConfirm() {
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
                e.stopImmediatePropagation();
            }
        });
    });
}

/* ─── Price formatter ───────────────────────────────────────── */
window.formatRupiah = (value) =>
    'Rp ' + parseInt(value || 0).toLocaleString('id-ID');

/* ─── Init all on DOM ready ─────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    initAutoDismiss();
    initAutoGrow();
    initDateInputs();
    initImagePreview();
    initConfirm();
});
