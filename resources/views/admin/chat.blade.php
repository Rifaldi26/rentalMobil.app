<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan — Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    <style>
        .chat-item {
            display: flex; align-items: center;
            padding: 14px 20px; border-bottom: 1px solid var(--gray-100);
            cursor: pointer; background: var(--white); transition: background .15s; gap: 12px;
        }
        .chat-item:hover { background: var(--gray-50); }
        .chat-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: var(--brand-100); color: var(--brand-600);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; flex-shrink: 0;
        }
        .chat-info { flex: 1; min-width: 0; }
        .chat-row-top { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px; }
        .chat-name { font-weight: 700; font-size: 14px; color: var(--gray-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-time { font-size: 11px; color: var(--gray-400); flex-shrink: 0; margin-left: 8px; }
        .chat-row-bottom { display: flex; justify-content: space-between; align-items: center; }
        .chat-preview { font-size: 13px; color: var(--gray-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-right: 8px; }
        .chat-unread {
            background: var(--brand-400); color: white; font-size: 11px; font-weight: 700;
            min-width: 20px; height: 20px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; padding: 0 5px; flex-shrink: 0;
        }
        .search-wrapper { position: relative; margin: 12px 20px 4px; }
        .search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--gray-400); pointer-events: none; }
        .search-input {
            width: 100%; background: var(--gray-50); border: 1.5px solid var(--gray-100); border-radius: 100px;
            padding: 10px 16px 10px 40px; font-family: var(--font); font-size: 14px;
            outline: none; box-sizing: border-box; color: var(--gray-900); transition: border-color .15s;
        }
        .search-input:focus { border-color: var(--brand-400); }
        #modal-chat { background: var(--white); }
        .room-header {
            display: flex; align-items: center; padding: 12px 16px;
            border-bottom: 1px solid var(--gray-100); background: var(--white);
            position: sticky; top: 0; z-index: 10;
        }
        .btn-icon {
            background: none; border: none; cursor: pointer; padding: 8px;
            color: var(--gray-500); display: flex; align-items: center; justify-content: center;
            border-radius: 50%; transition: background .15s;
        }
        .btn-icon:hover { background: var(--gray-100); }
        .room-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: var(--brand-100); color: var(--brand-600);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; margin: 0 12px 0 4px; flex-shrink: 0;
        }
        .room-title { font-weight: 700; font-size: 15px; color: var(--gray-900); }
        .room-status { font-size: 12px; color: var(--gray-500); display: flex; align-items: center; gap: 4px; margin-top: 1px; }
        .status-dot { width: 6px; height: 6px; background: var(--success); border-radius: 50%; }
        .header-actions { display: flex; gap: 4px; margin-left: auto; }
        .chat-area {
            flex: 1; overflow-y: auto; padding: 20px;
            background: var(--gray-50); display: flex; flex-direction: column; gap: 14px;
        }
        .bubble-divider { text-align: center; margin: 4px 0 8px; }
        .bubble-divider span {
            background: var(--white); border: 1px solid var(--gray-100);
            padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 500; color: var(--gray-400);
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
            border-radius: 24px; padding: 10px 16px; display: flex; align-items: center;
            transition: border-color .15s;
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

        /* Loading state */
        .chat-loading {
            text-align: center; padding: 32px; color: var(--gray-400); font-size: 13px;
        }
        /* Typing indicator */
        .typing-indicator {
            display: none; align-self: flex-start;
            background: var(--white); border: 1px solid var(--gray-100);
            border-radius: 18px 18px 18px 4px; padding: 10px 16px;
            box-shadow: var(--shadow-sm);
        }
        .typing-indicator.show { display: flex; gap: 4px; align-items: center; }
        .typing-dot {
            width: 7px; height: 7px; background: var(--gray-300);
            border-radius: 50%; animation: typingBounce 1.2s infinite;
        }
        .typing-dot:nth-child(2) { animation-delay: .2s; }
        .typing-dot:nth-child(3) { animation-delay: .4s; }
        @keyframes typingBounce {
            0%, 80%, 100% { transform: scale(1); opacity: .5; }
            40% { transform: scale(1.3); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="content" style="padding-bottom:100px;">
    <nav class="nav">
        <button onclick="window.location.href='{{ route('admin.dashboard') }}'"
            style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
        </button>
        <div class="nav-brand" style="font-size:16px;">Pesan</div>
        <div style="width:36px;"></div>
    </nav>

    <div class="search-wrapper">
        <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
        </svg>
        <input type="text" class="search-input" id="search-input"
               placeholder="Cari pelanggan..." oninput="filterChat()">
    </div>

    @if ($pelangganDenganPesan->isEmpty())
        <div style="text-align:center;padding:80px 20px;color:var(--gray-500);">
            <div style="font-size:48px;margin-bottom:12px;">💬</div>
            <div style="font-weight:700;color:var(--gray-900);font-size:15px;">Belum ada percakapan</div>
            <div style="font-size:13px;margin-top:6px;">Pesan dari pelanggan akan muncul di sini</div>
        </div>
    @else
        <div style="border-top:1px solid var(--gray-100);margin-top:8px;" id="chat-list">
            @foreach ($pelangganDenganPesan as $pelanggan)
                <div class="chat-item"
                     data-id="{{ $pelanggan->id }}"
                     data-nama="{{ strtolower($pelanggan->name) }}"
                     onclick="bukaChat({{ $pelanggan->id }}, '{{ addslashes($pelanggan->name) }}', '{{ strtoupper(substr($pelanggan->name,0,2)) }}')">
                    <div class="chat-avatar">{{ strtoupper(substr($pelanggan->name, 0, 2)) }}</div>
                    <div class="chat-info">
                        <div class="chat-row-top">
                            <div class="chat-name">{{ $pelanggan->name }}</div>
                            <div class="chat-time" id="waktu-{{ $pelanggan->id }}">
                                {{ $pelanggan->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="chat-row-bottom">
                            <div class="chat-preview" id="preview-{{ $pelanggan->id }}">Belum ada pesan</div>
                            <div class="chat-unread" id="unread-{{ $pelanggan->id }}" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div id="empty-search" style="display:none;text-align:center;padding:48px 20px;color:var(--gray-500);">
            <div style="font-size:36px;margin-bottom:8px;">🔍</div>
            <div style="font-weight:600;font-size:14px;">Pelanggan tidak ditemukan</div>
        </div>
    @endif
</div>

{{-- ══ MODAL CHAT ════════════════════════════════════════════ --}}
<div id="modal-chat" style="display:none;position:fixed;inset:0;z-index:200;flex-direction:column;">

    <div class="room-header">
        <button onclick="tutupChat()" class="btn-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
        </button>
        <div id="chat-avatar" class="room-avatar"></div>
        <div>
            <div id="chat-nama" class="room-title"></div>
            <div class="room-status"><div class="status-dot"></div> Pelanggan</div>
        </div>
        <div class="header-actions">
            <button class="btn-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.11 12 19.79 19.79 0 0 1 1.04 3.33 2 2 0 0 1 3 1h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
            </button>
        </div>
    </div>

    <div id="chat-messages" class="chat-area">
        <div class="chat-loading" id="chat-loading">Memuat pesan...</div>
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

@include('admin.partials.bottom-nav')

<script>
var chatLawanId  = null;
var adminId      = {{ Auth::id() }};
var echoChannel  = null;

/* ── Filter search ── */
function filterChat() {
    var keyword = document.getElementById('search-input').value.toLowerCase().trim();
    var items   = document.querySelectorAll('#chat-list .chat-item');
    var visible = 0;
    items.forEach(function(item) {
        var show = !keyword || item.dataset.nama.includes(keyword);
        item.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    var el = document.getElementById('empty-search');
    if (el) el.style.display = visible === 0 ? 'block' : 'none';
}

/* ── Buka chat ── */
function bukaChat(lawanId, lawanNama, inisial) {
    chatLawanId = lawanId;
    document.getElementById('chat-nama').textContent   = lawanNama;
    document.getElementById('chat-avatar').textContent = inisial;
    document.getElementById('modal-chat').style.display = 'flex';

    // Reset unread badge
    var badge = document.getElementById('unread-' + lawanId);
    if (badge) badge.style.display = 'none';

    // Subscribe dulu, muat riwayat setelah modal terlihat
    subscribeChannel(lawanId);
    
    // Delay kecil agar DOM modal sudah aktif sebelum fetch
    setTimeout(function() {
        muatRiwayat(lawanId);
    }, 50);
}

function tutupChat() {
    // Unsubscribe channel
    if (chatLawanId) {
        window.Echo.leave(namaChannel(chatLawanId));
        echoChannel = null;
    }

    // Reset modal
    document.getElementById('modal-chat').style.display = 'none';
    document.getElementById('chat-input').value = '';
    document.getElementById('chat-input').style.height = 'auto';
    document.getElementById('btn-send').classList.remove('active');

    // Reset container pesan agar tidak dobel saat dibuka lagi
    document.getElementById('chat-messages').innerHTML =
        '<div class="chat-loading" id="chat-loading">Memuat pesan...</div>';

    chatLawanId = null;
}

/* ── Nama channel (sorted ID) ── */
function namaChannel(lawanId) {
    var ids = [adminId, lawanId].sort(function(a, b) { return a - b; });
    return 'chat.' + ids[0] + '-' + ids[1];
}

/* ── Subscribe Echo ── */
function subscribeChannel(lawanId) {
    if (echoChannel) {
        window.Echo.leave(namaChannel(chatLawanId));
    }
    echoChannel = window.Echo.private(namaChannel(lawanId))
        .listen('.pesan.baru', function(data) {
            // Hanya tampilkan jika modal terbuka untuk user ini
            if (chatLawanId === data.pengirim_id || chatLawanId === data.penerima_id) {
                var dari = data.pengirim_id === adminId ? 'saya' : 'lawan';
                appendBubble(dari, data.isi, data.waktu);
            }
        });
}

/* ── Muat riwayat via fetch ── */
function muatRiwayat(lawanId) {
    var container = document.getElementById('chat-messages');
    container.innerHTML = '<div class="chat-loading">Memuat pesan...</div>';

    fetch('/chat/' + lawanId + '/pesan', {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrf() }
    })
    .then(function(r) { return r.json(); })
    .then(function(pesans) {
        container.innerHTML = '';

        var divider = document.createElement('div');
        divider.className = 'bubble-divider';
        divider.innerHTML = '<span>Hari ini</span>';
        container.appendChild(divider);

        pesans.forEach(function(p) {
            var dari = p.pengirim_id === adminId ? 'saya' : 'lawan';
            appendBubble(dari, p.isi, p.waktu);
        });

        // Typing indicator
        var typing = document.createElement('div');
        typing.className = 'typing-indicator';
        typing.id = 'typing';
        typing.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
        container.appendChild(typing);

        scrollKeBawah();
    })
    .catch(function() {
        container.innerHTML = '<div class="chat-loading">Gagal memuat pesan.</div>';
    });
}

/* ── Append satu bubble ── */
function appendBubble(dari, isi, waktu) {
    var container = document.getElementById('chat-messages');
    var typing    = document.getElementById('typing');

    var wrap   = document.createElement('div');
    wrap.className = 'bubble-wrap ' + dari;

    var bubble = document.createElement('div');
    bubble.className  = 'bubble ' + dari;
    bubble.textContent = isi;

    var time = document.createElement('div');
    time.className  = 'bubble-time';
    time.textContent = waktu;

    wrap.appendChild(bubble);
    wrap.appendChild(time);

    // Sisipkan sebelum typing indicator
    if (typing) container.insertBefore(wrap, typing);
    else container.appendChild(wrap);

    scrollKeBawah();
}

/* ── Kirim pesan ── */
function kirimPesan() {
    var input = document.getElementById('chat-input');
    var teks  = input.value.trim();
    if (!teks || !chatLawanId) return;

    // Optimistic UI
    var waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    appendBubble('saya', teks, waktu);
    updatePreview(chatLawanId, teks, waktu);
    input.value = '';
    input.style.height = 'auto';
    document.getElementById('btn-send').classList.remove('active');

    // Kirim ke server
    fetch('/chat/' + chatLawanId + '/kirim', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': getCsrf()
        },
        body: JSON.stringify({ isi: teks })
    }).catch(function() {
        console.error('Gagal kirim pesan');
    });
}

/* ── Update preview di daftar ── */
function updatePreview(lawanId, teks, waktu) {
    var el = document.getElementById('preview-' + lawanId);
    var wEl = document.getElementById('waktu-' + lawanId);
    if (el) el.textContent = 'Kamu: ' + teks.substring(0, 40);
    if (wEl) wEl.textContent = waktu;
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

/* ── Subscribe ke SEMUA channel pelanggan untuk notif badge ── */
function initBadgeListeners() {
    if (!window.Echo) {
        setTimeout(initBadgeListeners, 200); // tunggu Echo siap
        return;
    }
    @foreach ($pelangganDenganPesan as $pelanggan)
    (function() {
        var ids = [adminId, {{ $pelanggan->id }}].sort(function(a,b){return a-b;});
        var ch  = 'chat.' + ids[0] + '-' + ids[1];
        window.Echo.private(ch).listen('.pesan.baru', function(data) {
            if (data.pengirim_id !== adminId && chatLawanId !== data.pengirim_id) {
                var badge = document.getElementById('unread-' + data.pengirim_id);
                var prev  = document.getElementById('preview-' + data.pengirim_id);
                var wEl   = document.getElementById('waktu-' + data.pengirim_id);
                if (badge) { badge.style.display = 'flex'; badge.textContent = (parseInt(badge.textContent) || 0) + 1; }
                if (prev)  prev.textContent = data.isi.substring(0, 40);
                if (wEl)   wEl.textContent  = data.waktu;
            }
        });
    })();
    @endforeach
}
initBadgeListeners();
</script>

</body>
</html>