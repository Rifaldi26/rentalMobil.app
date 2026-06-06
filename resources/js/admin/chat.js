/**
 * Admin Chat — WebSocket (Laravel Echo) + fetch riwayat pesan.
 */

let chatLawanId = null;
let echoChannel = null;
const adminId   = window.adminUserId; // di-set dari blade

// ── Helpers ─────────────────────────────────────────────────
const getCsrf  = () => document.querySelector('meta[name="csrf-token"]').content;
const namaChannel = id => {
    const ids = [adminId, id].sort((a, b) => a - b);
    return `chat.${ids[0]}-${ids[1]}`;
};

// ── Filter search ────────────────────────────────────────────
function filterChat() {
    const keyword = document.getElementById('search-input').value.toLowerCase().trim();
    let visible   = 0;

    document.querySelectorAll('#chat-list .chat-item').forEach(item => {
        const show = !keyword || item.dataset.nama.includes(keyword);
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    const el = document.getElementById('empty-search');
    if (el) el.style.display = visible === 0 ? 'block' : 'none';
}

// ── Buka / Tutup chat ────────────────────────────────────────
function bukaChat(lawanId, lawanNama, inisial) {
    chatLawanId = lawanId;
    document.getElementById('chat-nama').textContent    = lawanNama;
    document.getElementById('chat-avatar').textContent  = inisial;
    document.getElementById('modal-chat').classList.add('open');

    const badge = document.getElementById(`unread-${lawanId}`);
    if (badge) badge.style.display = 'none';

    subscribeChannel(lawanId);
    setTimeout(() => muatRiwayat(lawanId), 50);
}

function tutupChat() {
    if (chatLawanId) {
        window.Echo.leave(namaChannel(chatLawanId));
        echoChannel = null;
    }

    document.getElementById('modal-chat').classList.remove('open');
    document.getElementById('chat-input').value        = '';
    document.getElementById('chat-input').style.height = 'auto';
    document.getElementById('btn-send').classList.remove('active');
    document.getElementById('chat-messages').innerHTML =
        '<div class="chat-loading">Memuat pesan...</div>';

    chatLawanId = null;
}

// ── Echo subscribe ───────────────────────────────────────────
function subscribeChannel(lawanId) {
    if (echoChannel) window.Echo.leave(namaChannel(chatLawanId));

    echoChannel = window.Echo.private(namaChannel(lawanId))
        .listen('.pesan.baru', data => {
            if (chatLawanId === data.pengirim_id || chatLawanId === data.penerima_id) {
                appendBubble(data.pengirim_id === adminId ? 'sent' : 'received', data.isi, data.waktu);
            }
        });
}

// ── Load riwayat ─────────────────────────────────────────────
function muatRiwayat(lawanId) {
    const container = document.getElementById('chat-messages');
    container.innerHTML = '<div class="chat-loading">Memuat pesan...</div>';

    fetch(`/chat/${lawanId}/pesan`, {
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': getCsrf() },
    })
    .then(r => r.json())
    .then(pesans => {
        container.innerHTML = '<div class="chat-date-divider"><span>Hari ini</span></div>';
        pesans.forEach(p => appendBubble(
            p.pengirim_id === adminId ? 'sent' : 'received', p.isi, p.waktu
        ));
        scrollKeBawah();
    })
    .catch(() => {
        container.innerHTML = '<div class="chat-loading">Gagal memuat pesan.</div>';
    });
}

// ── Append bubble ────────────────────────────────────────────
function appendBubble(arah, isi, waktu) {
    const container = document.getElementById('chat-messages');

    const wrap   = document.createElement('div');
    wrap.className = `message-wrap message-wrap--${arah}`;

    const bubble = document.createElement('div');
    bubble.className  = `message-bubble ${arah}`;
    bubble.textContent = isi;

    const time = document.createElement('div');
    time.className  = 'message-time';
    time.textContent = waktu;

    wrap.appendChild(bubble);
    wrap.appendChild(time);
    container.appendChild(wrap);
    scrollKeBawah();
}

// ── Kirim pesan ──────────────────────────────────────────────
function kirimPesan() {
    const input = document.getElementById('chat-input');
    const teks  = input.value.trim();
    if (!teks || !chatLawanId) return;

    const waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    appendBubble('sent', teks, waktu);
    updateChatPreview(chatLawanId, teks, waktu);

    input.value        = '';
    input.style.height = 'auto';
    document.getElementById('btn-send').classList.remove('active');

    fetch(`/chat/${chatLawanId}/kirim`, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': getCsrf() },
        body:    JSON.stringify({ isi: teks }),
    }).catch(() => {});
}

function updateChatPreview(lawanId, teks, waktu) {
    const previewEl = document.getElementById(`preview-${lawanId}`);
    const waktuEl   = document.getElementById(`waktu-${lawanId}`);
    if (previewEl) previewEl.textContent = 'Kamu: ' + teks.substring(0, 40);
    if (waktuEl)   waktuEl.textContent   = waktu;
}

function handleChatInput(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    document.getElementById('btn-send').classList.toggle('active', el.value.trim().length > 0);
}

function enterKirim(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); kirimPesan(); }
}

function scrollKeBawah() {
    const el = document.getElementById('chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
}