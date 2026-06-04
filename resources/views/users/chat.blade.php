<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat — Rental Mobil</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    <style>
        .chat-area {
            flex: 1; overflow-y: auto; padding: 20px;
            background: var(--gray-50); display: flex; flex-direction: column; gap: 14px;
        }
        .bubble-divider { text-align: center; margin: 4px 0 8px; }
        .bubble-divider span {
            background: var(--white); border: 1px solid var(--gray-100);
            padding: 4px 14px; border-radius: 20px;
            font-size: 11px; font-weight: 500; color: var(--gray-400);
        }
        .bubble-wrap { display: flex; flex-direction: column; max-width: 75%; }
        .bubble-wrap.lawan { align-self: flex-start; }
        .bubble-wrap.saya  { align-self: flex-end; }
        .bubble { padding: 10px 16px; font-size: 14px; line-height: 1.5; font-family: var(--font); box-shadow: var(--shadow-sm); }
        .bubble.lawan { background: var(--white); border: 1px solid var(--gray-100); color: var(--gray-900); border-radius: 18px 18px 18px 4px; }
        .bubble.saya  { background: var(--brand-400); color: var(--white); border-radius: 18px 18px 4px 18px; }
        .bubble-time { font-size: 11px; color: var(--gray-400); margin-top: 4px; }
        .bubble-wrap.lawan .bubble-time { align-self: flex-start; margin-left: 4px; }
        .bubble-wrap.saya  .bubble-time { align-self: flex-end;   margin-right: 4px; }
        .input-area {
            padding: 12px 16px; border-top: 1px solid var(--gray-100);
            background: var(--white); display: flex; gap: 10px; align-items: flex-end;
        }
        .input-box {
            flex: 1; background: var(--gray-50); border: 1.5px solid var(--gray-100);
            border-radius: 24px; padding: 10px 16px;
            display: flex; align-items: center; transition: border-color .15s;
        }
        .input-box:focus-within { border-color: var(--brand-400); }
        .input-box textarea {
            width: 100%; background: none; border: none; outline: none;
            font-family: var(--font); font-size: 14px; color: var(--gray-900);
            resize: none; max-height: 100px; padding: 0; line-height: 1.4;
        }
        .input-box textarea::placeholder { color: var(--gray-400); }
        .btn-send {
            width: 44px; height: 44px;
            background: var(--brand-50); color: var(--brand-300);
            border: 1.5px solid var(--brand-100); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0; transition: background .2s, transform .15s;
        }
        .btn-send:active { transform: scale(0.92); }
        .btn-send.active { background: var(--brand-400); color: var(--white); border-color: var(--brand-400); }
        .admin-header {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 20px; border-bottom: 1px solid var(--gray-100);
            background: var(--white); position: sticky; top: var(--nav-h); z-index: 5;
        }
        .admin-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: var(--brand-400); color: var(--white);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 15px; flex-shrink: 0;
        }
        .admin-name { font-weight: 700; font-size: 14px; color: var(--gray-900); }
        .admin-status { font-size: 12px; color: var(--success); display: flex; align-items: center; gap: 4px; margin-top: 1px; }
        .status-dot { width: 6px; height: 6px; background: var(--success); border-radius: 50%; }
        .typing-indicator { display: none; align-self: flex-start; gap: 4px; align-items: center; padding: 10px 16px; background: var(--white); border: 1px solid var(--gray-100); border-radius: 18px 18px 18px 4px; box-shadow: var(--shadow-sm); }
        .typing-indicator.show { display: flex; }
        .typing-dot { width: 7px; height: 7px; background: var(--gray-300); border-radius: 50%; animation: typingBounce 1.2s infinite; }
        .typing-dot:nth-child(2) { animation-delay: .2s; }
        .typing-dot:nth-child(3) { animation-delay: .4s; }
        @keyframes typingBounce { 0%,80%,100%{transform:scale(1);opacity:.5} 40%{transform:scale(1.3);opacity:1} }
        .empty-chat { text-align: center; padding: 48px 20px; color: var(--gray-500); }
    </style>
</head>
<body class="user-page">
@include('users.partials.desktop-sidebar')
<nav class="nav">
    <button onclick="history.back()"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Chat</div>
    <div style="width:36px;"></div>
</nav>

@php $adminId = $admin?->id; @endphp

@if (!$admin)
    <div class="content" style="text-align:center;padding:80px 20px;color:var(--gray-500);">
        <div style="font-size:48px;margin-bottom:12px;">🔧</div>
        <div style="font-weight:700;font-size:15px;color:var(--gray-900);">Admin belum tersedia</div>
        <div style="font-size:13px;margin-top:6px;">Silakan coba lagi nanti</div>
    </div>
@else
    {{-- Header info admin --}}
    <div class="admin-header" style="margin-top:var(--nav-h);">
        <div class="admin-avatar">AD</div>
        <div>
            <div class="admin-name">Tim Rental Mobil</div>
            <div class="admin-status">
                <div class="status-dot"></div> Online
            </div>
        </div>
    </div>

    {{-- Area chat (flex column, full height) --}}
    <div style="display:flex;flex-direction:column;height:calc(100vh - var(--nav-h) - 60px - var(--bottom-h));">

        <div id="chat-messages" class="chat-area">
            <div class="bubble-divider"><span>Hari ini</span></div>

            <div class="empty-chat" id="empty-chat">
                <div style="font-size:40px;margin-bottom:10px;">💬</div>
                <div style="font-weight:600;font-size:14px;color:var(--gray-700);">Mulai percakapan</div>
                <div style="font-size:13px;margin-top:4px;">Tanyakan apapun tentang rental mobil kepada kami</div>
            </div>

            <div class="typing-indicator" id="typing">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>

        <div class="input-area">
            <div class="input-box">
                <textarea id="chat-input" rows="1"
                          placeholder="Tulis pesan..."
                          oninput="handleInput(this)"
                          onkeydown="enterKirim(event)"></textarea>
            </div>
            <button id="btn-send" onclick="kirimPesan()" class="btn-send">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" style="margin-left:2px;">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>
    </div>
@endif

@if(Auth::user()->role === 'admin')
    @include('admin.partials.bottom-nav')
@else
    @include('users.partials.bottom-nav')
@endif
<script>
var userId  = {{ Auth::id() }};
var adminId = {{ $adminId ?? 'null' }};

if (adminId) {
    // Muat riwayat saat halaman dibuka
    muatRiwayat();

    // Subscribe channel privat
    var ids = [userId, adminId].sort(function(a,b){return a-b;});
    window.Echo.private('chat.' + ids[0] + '-' + ids[1])
        .listen('.pesan.baru', function(data) {
            if (data.pengirim_id === adminId) {
                var empty = document.getElementById('empty-chat');
                if (empty) empty.style.display = 'none';
                appendBubble('lawan', data.isi, data.waktu);
            }
        });
}

function muatRiwayat() {
    fetch('/chat/' + adminId + '/pesan', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf() }
    })
    .then(function(r) { return r.json(); })
    .then(function(pesans) {
        if (pesans.length === 0) return;

        var empty = document.getElementById('empty-chat');
        if (empty) empty.style.display = 'none';

        pesans.forEach(function(p) {
            var dari = p.pengirim_id === userId ? 'saya' : 'lawan';
            appendBubble(dari, p.isi, p.waktu);
        });
        scrollKeBawah();
    });
}

function appendBubble(dari, isi, waktu) {
    var container = document.getElementById('chat-messages');
    var typing    = document.getElementById('typing');

    var wrap = document.createElement('div');
    wrap.className = 'bubble-wrap ' + dari;

    var bubble = document.createElement('div');
    bubble.className  = 'bubble ' + dari;
    bubble.textContent = isi;

    var time = document.createElement('div');
    time.className  = 'bubble-time';
    time.textContent = waktu;

    wrap.appendChild(bubble);
    wrap.appendChild(time);
    if (typing) container.insertBefore(wrap, typing);
    else container.appendChild(wrap);

    scrollKeBawah();
}

function kirimPesan() {
    var input = document.getElementById('chat-input');
    var teks  = input.value.trim();
    if (!teks || !adminId) return;

    var waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    var empty = document.getElementById('empty-chat');
    if (empty) empty.style.display = 'none';

    appendBubble('saya', teks, waktu);
    input.value = '';
    input.style.height = 'auto';
    document.getElementById('btn-send').classList.remove('active');

    fetch('/chat/' + adminId + '/kirim', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrf()
        },
        body: JSON.stringify({ isi: teks })
    }).catch(function() { console.error('Gagal kirim pesan'); });
}

function enterKirim(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); kirimPesan(); }
}

function handleInput(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    document.getElementById('btn-send').classList.toggle('active', el.value.trim().length > 0);
}

function scrollKeBawah() {
    var el = document.getElementById('chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
}

function getCsrf() {
    return document.querySelector('meta[name="csrf-token"]').content;
}
</script>

</body>
</html>