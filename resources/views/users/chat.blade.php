<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

<nav class="nav">
    <button onclick="window.location.href='{{ route('dashboard') }}'"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Pesan</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding-bottom:100px;">

    {{-- List Percakapan --}}
    <div style="padding-top:8px;">
        {{-- Admin --}}
        <div class="chat-item" onclick="bukaChat('admin', 'Admin Rental', 'AD')">
            <div class="chat-avatar" style="background:var(--brand-100);color:var(--brand-600);">AD</div>
            <div class="chat-meta">
                <div class="chat-name">Admin Rental</div>
                <div class="chat-preview">Ada yang bisa kami bantu? 😊</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;">
                <div class="chat-time">Tadi</div>
            </div>
        </div>
    </div>

    {{-- Empty state jika tidak ada percakapan --}}
    {{--
    <div style="text-align:center;padding:80px 20px;color:var(--gray-500);">
        <div style="font-size:48px;margin-bottom:12px;">💬</div>
        <div style="font-weight:600;">Belum ada percakapan</div>
        <div style="font-size:13px;margin-top:4px;">Hubungi admin jika ada pertanyaan</div>
        <button onclick="bukaChat('admin','Admin Rental','AD')"
            style="margin-top:16px;padding:10px 20px;background:var(--brand-400);color:#fff;border:none;border-radius:var(--radius-md);font-size:13px;font-weight:700;cursor:pointer;">
            💬 Hubungi Admin
        </button>
    </div>
    --}}

</div>

{{-- ═══ MODAL RUANG CHAT ═══════════════════════════════════ --}}
<div id="modal-chat" style="display:none;position:fixed;inset:0;z-index:200;flex-direction:column;background:#fff;">

    {{-- Header --}}
    <div style="display:flex;align-items:center;gap:12px;padding:14px 16px;border-bottom:1px solid var(--gray-100);background:#fff;position:sticky;top:0;">
        <button onclick="tutupChat()"
            style="background:none;border:none;cursor:pointer;padding:4px;display:flex;align-items:center;color:var(--gray-700);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
        </button>
        <div id="chat-avatar"
             style="width:36px;height:36px;border-radius:50%;background:var(--brand-100);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--brand-600);flex-shrink:0;">
        </div>
        <div>
            <div id="chat-nama" style="font-size:15px;font-weight:700;color:var(--gray-900);"></div>
            <div style="font-size:11px;color:var(--success);">● Online</div>
        </div>
    </div>

    {{-- Pesan --}}
    <div id="chat-messages"
         style="flex:1;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;background:#f8fafc;">
    </div>

    {{-- Input --}}
    <div style="display:flex;gap:10px;padding:12px 16px;border-top:1px solid var(--gray-100);background:#fff;align-items:flex-end;">
        <div style="flex:1;background:var(--gray-100);border-radius:20px;padding:10px 16px;">
            <textarea id="chat-input"
                rows="1"
                placeholder="Ketik pesan..."
                style="background:none;border:none;outline:none;font-family:var(--font);font-size:14px;color:var(--gray-900);width:100%;resize:none;max-height:100px;line-height:1.4;"
                oninput="autoResize(this)"
                onkeydown="enterKirim(event)"></textarea>
        </div>
        <button onclick="kirimPesan()"
            style="width:42px;height:42px;background:var(--brand-400);border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round">
                <path d="M22 2L11 13M22 2L15 22l-4-9-9-4 20-7z"/>
            </svg>
        </button>
    </div>
</div>

@include('users.partials.bottom-nav')

<script>
let chatLawanId  = null;
let chatMessages = {};

const dummyPesan = [
    { dari: 'lawan', pesan: 'Halo! Ada yang bisa kami bantu? 😊', waktu: '09.00' },
    { dari: 'saya',  pesan: 'Halo, saya mau tanya soal pemesanan saya', waktu: '09.01' },
    { dari: 'lawan', pesan: 'Tentu, bisa ceritakan lebih lanjut?', waktu: '09.02' },
];

function bukaChat(lawanId, lawanNama, inisial) {
    chatLawanId = lawanId;
    document.getElementById('chat-nama').textContent   = lawanNama;
    document.getElementById('chat-avatar').textContent = inisial;
    renderPesan(lawanId);
    const modal = document.getElementById('modal-chat');
    modal.style.display = 'flex';
    modal.style.flexDirection = 'column';
    setTimeout(scrollKeBawah, 100);
}

function tutupChat() {
    document.getElementById('modal-chat').style.display = 'none';
    document.getElementById('chat-input').value = '';
    chatLawanId = null;
}

function renderPesan(lawanId) {
    const container = document.getElementById('chat-messages');
    container.innerHTML = '';

    const divider = document.createElement('div');
    divider.className = 'bubble-date-divider';
    divider.textContent = 'Hari ini';
    container.appendChild(divider);

    const pesans = chatMessages[lawanId] || dummyPesan;
    pesans.forEach(m => {
        const wrap = document.createElement('div');
        wrap.className = `bubble-wrap ${m.dari}`;
        const bubble = document.createElement('div');
        bubble.className = `bubble ${m.dari}`;
        bubble.textContent = m.pesan;
        const time = document.createElement('div');
        time.className = 'bubble-time';
        time.textContent = m.waktu;
        wrap.appendChild(bubble);
        wrap.appendChild(time);
        container.appendChild(wrap);
    });
}

function kirimPesan() {
    const input = document.getElementById('chat-input');
    const teks  = input.value.trim();
    if (!teks || !chatLawanId) return;
    const waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    if (!chatMessages[chatLawanId]) chatMessages[chatLawanId] = [...dummyPesan];
    chatMessages[chatLawanId].push({ dari: 'saya', pesan: teks, waktu });
    renderPesan(chatLawanId);
    input.value = '';
    input.style.height = 'auto';
    scrollKeBawah();
}

function enterKirim(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); kirimPesan(); }
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
}

function scrollKeBawah() {
    const el = document.getElementById('chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
}
</script>

</body>
</html>