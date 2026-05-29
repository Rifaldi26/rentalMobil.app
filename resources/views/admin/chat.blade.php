<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan — Admin</title>
    @vite(['resources/css/dashboard.css'])
    <style>
        /* =========================================
           STYLE KHUSUS TAMPILAN CHAT
           ========================================= */
        :root {
            --chat-dark: #0f172a; /* Warna biru gelap/hitam untuk avatar & bubble admin */
            --text-main: #111827;
            --text-muted: #6b7280;
            --bg-body: #ffffff;
            --border-light: #f3f4f6;
        }

        body {
            background-color: var(--bg-body);
            margin: 0;
            font-family: sans-serif;
        }

        /* Header Utama */
        .chat-main-header {
            padding: 20px 20px 10px 20px;
        }
        .chat-main-header h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
        }

        /* Bar Pencarian */
        .search-wrapper {
            position: relative;
            margin: 16px 20px;
        }
        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }
        .search-input {
            width: 100%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 9999px;
            padding: 10px 16px 10px 40px;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .search-input:focus {
            border-color: #cbd5e1;
        }

        /* Daftar Chat */
        .chat-item {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-light);
            cursor: pointer;
            background: #fff;
            transition: background 0.2s;
        }
        .chat-item:hover { background: #f9fafb; }
        .chat-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--chat-dark);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            flex-shrink: 0;
            margin-right: 14px;
        }
        .chat-info {
            flex: 1;
            min-width: 0;
        }
        .chat-row-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 4px;
        }
        .chat-name {
            font-weight: 600;
            font-size: 15px;
            color: var(--text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .chat-time {
            font-size: 12px;
            color: var(--text-muted);
            flex-shrink: 0;
            margin-left: 8px;
        }
        .chat-row-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-preview {
            font-size: 14px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            padding-right: 10px;
        }
        .badge-unread {
            background: var(--chat-dark);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }

        /* =========================================
           MODAL RUANG CHAT
           ========================================= */
        #modal-chat { background: #ffffff; }
        
        .room-header {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-light);
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: #4b5563;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .btn-icon:hover { background: #f3f4f6; }
        .room-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--chat-dark);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            margin: 0 12px 0 4px;
            flex-shrink: 0;
        }
        .room-title {
            font-weight: 600;
            font-size: 16px;
            color: var(--text-main);
        }
        .room-status {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 2px;
        }
        .status-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
        }
        .header-actions {
            display: flex;
            gap: 4px;
        }

        /* Area Pesan */
        .chat-area {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .bubble-divider {
            text-align: center;
            margin: 8px 0 16px;
        }
        .bubble-divider span {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .bubble-wrap {
            display: flex;
            flex-direction: column;
            max-width: 75%;
        }
        .bubble-wrap.lawan { align-self: flex-start; }
        .bubble-wrap.saya { align-self: flex-end; }

        .bubble {
            padding: 10px 16px;
            font-size: 14px;
            line-height: 1.5;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        /* Desain Gelembung Kiri (Lawan) */
        .bubble.lawan {
            background: #ffffff;
            border: 1px solid #f3f4f6;
            color: var(--text-main);
            border-radius: 20px 20px 20px 4px;
        }
        /* Desain Gelembung Kanan (Admin/Saya) */
        .bubble.saya {
            background: var(--chat-dark);
            color: #ffffff;
            border-radius: 20px 20px 4px 20px;
        }

        .bubble-time {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }
        .bubble-wrap.lawan .bubble-time { align-self: flex-start; margin-left: 4px; }
        .bubble-wrap.saya .bubble-time { align-self: flex-end; margin-right: 4px; }

        /* Area Input */
        .input-area {
            padding: 12px 16px;
            border-top: 1px solid var(--border-light);
            background: #fff;
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        .input-box {
            flex: 1;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
        }
        .input-box textarea {
            width: 100%;
            background: none;
            border: none;
            outline: none;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            resize: none;
            max-height: 100px;
            padding: 0;
            line-height: 1.4;
        }
        .btn-send {
            width: 44px;
            height: 44px;
            background: #9ca3af;
            color: #fff;
            border: none;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            flex-shrink: 0;
            transition: background 0.2s;
        }
        .btn-send.active { background: var(--chat-dark); }
    </style>
</head>
<body>

<div class="content" style="padding-top: 10px !important; margin-top: 0 !important; padding-bottom: 100px;">
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

    {{-- Pencarian --}}
    <div class="search-wrapper">
        <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" class="search-input" placeholder="Cari pelanggan...">
    </div>

    {{-- ─── List Percakapan ─── --}}
    @php
        $pelangganDenganPesan = \App\Models\User::where('role', 'pelanggan')
            ->latest()
            ->get();
    @endphp

    @if ($pelangganDenganPesan->isEmpty())
        <div style="text-align:center;padding:80px 20px;color:var(--text-muted);">
            <div style="font-size:48px;margin-bottom:12px;">💬</div>
            <div style="font-weight:600;color:var(--text-main);">Belum ada percakapan</div>
            <div style="font-size:13px;margin-top:4px;">Pesan dari pelanggan akan muncul di sini</div>
        </div>
    @else
        <div style="border-top: 1px solid var(--border-light);">
            @foreach ($pelangganDenganPesan as $pelanggan)
                <div class="chat-item" onclick="bukaChat({{ $pelanggan->id }}, '{{ addslashes($pelanggan->name) }}', '{{ strtoupper(substr($pelanggan->name, 0, 2)) }}')">
                    
                    <div class="chat-avatar">
                        {{ strtoupper(substr($pelanggan->name, 0, 2)) }}
                    </div>
                    
                    <div class="chat-info">
                        <div class="chat-row-top">
                            <div class="chat-name">{{ $pelanggan->name }}</div>
                            <div class="chat-time">{{ $pelanggan->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="chat-row-bottom">
                            <div class="chat-preview">{{ $pelanggan->no_hp ?? 'Menunggu balasan...' }}</div>
                            {{-- Badge unread opsional, bisa dihubungkan ke logic database nanti --}}
                            <div class="badge-unread" style="display: none;">0</div> 
                        </div>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ═══ MODAL RUANG CHAT ═══════════════════════════════════ --}}
<div id="modal-chat" style="display:none;position:fixed;inset:0;z-index:200;flex-direction:column;">

    {{-- Header --}}
    <div class="room-header">
        <button onclick="tutupChat()" class="btn-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>
        
        <div id="chat-avatar" class="room-avatar"></div>
        
        <div style="flex:1;">
            <div id="chat-nama" class="room-title"></div>
            <div class="room-status">
                <div class="status-dot"></div> Pelanggan • Online
            </div>
        </div>

        <div class="header-actions">
            <button class="btn-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
            </button>
            <button class="btn-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle>
                </svg>
            </button>
        </div>
    </div>

    {{-- Pesan --}}
    <div id="chat-messages" class="chat-area"></div>

    {{-- Input --}}
    <div class="input-area">
        <div class="input-box">
            <textarea id="chat-input"
                rows="1"
                placeholder="Tulis pesan..."
                oninput="handleInput(this)"
                onkeydown="enterKirim(event)"></textarea>
        </div>
        <button id="btn-send" onclick="kirimPesan()" class="btn-send">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 2px;">
                <line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
        </button>
    </div>
</div>

@include('admin.partials.bottom-nav')

<script>
let chatLawanId   = null;
let chatMessages  = {};

const dummyPesan = [
    { dari: 'lawan', pesan: 'Halo admin, saya mau tanya soal pemesanan saya', waktu: '09.01' },
    { dari: 'saya',  pesan: 'Halo! Ada yang bisa kami bantu? 😊', waktu: '09.02' },
    { dari: 'lawan', pesan: 'Kapan mobil saya bisa dikonfirmasi?', waktu: '09.03' },
    { dari: 'saya',  pesan: 'Akan segera kami proses, mohon tunggu sebentar 🙏', waktu: '09.05' },
];

function bukaChat(lawanId, lawanNama, inisial) {
    chatLawanId = lawanId;

    document.getElementById('chat-nama').textContent   = lawanNama;
    document.getElementById('chat-avatar').textContent = inisial;

    renderPesan(lawanId);

    const modal = document.getElementById('modal-chat');
    modal.style.display = 'flex';
    
    setTimeout(scrollKeBawah, 100);
}

function tutupChat() {
    document.getElementById('modal-chat').style.display = 'none';
    document.getElementById('chat-input').value = '';
    document.getElementById('btn-send').classList.remove('active');
    chatLawanId = null;
}

function renderPesan(lawanId) {
    const container = document.getElementById('chat-messages');
    container.innerHTML = '';

    const divider = document.createElement('div');
    divider.className = 'bubble-divider';
    divider.innerHTML = '<span>Hari ini</span>';
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
    const btnSend = document.getElementById('btn-send');
    const teks  = input.value.trim();
    if (!teks || !chatLawanId) return;

    const waktu = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

    if (!chatMessages[chatLawanId]) {
        chatMessages[chatLawanId] = [...dummyPesan];
    }
    chatMessages[chatLawanId].push({ dari: 'saya', pesan: teks, waktu });

    renderPesan(chatLawanId);
    input.value = '';
    input.style.height = 'auto';
    btnSend.classList.remove('active');
    scrollKeBawah();
}

function enterKirim(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        kirimPesan();
    }
}

function handleInput(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    
    // Toggle warna tombol kirim berdasarkan isi input
    const btnSend = document.getElementById('btn-send');
    if (el.value.trim().length > 0) {
        btnSend.classList.add('active');
    } else {
        btnSend.classList.remove('active');
    }
}

function scrollKeBawah() {
    const el = document.getElementById('chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
}
</script>

</body>
</html>