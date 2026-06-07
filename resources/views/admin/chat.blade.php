@extends('layouts.admin')
@section('title', 'Chat Pelanggan')
@section('page-title', 'Chat Pelanggan')

@push('styles')
    @vite(['resources/css/admin.css'])
@endpush

@section('content')
<div class="admin-content admin-content--flush">

    <div class="chat-layout">

        {{-- Panel kiri: daftar pelanggan --}}
        <div class="chat-list-panel">

            <div class="chat-list-header">Percakapan</div>

            <div class="chat-search-wrapper">
                <div class="chat-search-inner">
                    <svg class="chat-search-icon" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" class="chat-search-input" id="search-input"
                           placeholder="Cari pelanggan...">
                </div>
            </div>

            <div class="chat-list-scroll" id="chat-list">
                @if ($pelangganDenganPesan->isEmpty())
                    <div class="empty-state empty-state--lg">
                        <div class="empty-state__icon">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div class="empty-state__title">Belum ada percakapan</div>
                        <div class="empty-state__sub">Pesan dari pelanggan akan muncul di sini</div>
                    </div>
                @else
                    @foreach ($pelangganDenganPesan as $pelanggan)
                        <div class="chat-item"
                             data-id="{{ $pelanggan->id }}"
                             data-nama="{{ strtolower($pelanggan->name) }}"
                             data-fullname="{{ addslashes($pelanggan->name) }}"
                             data-inisial="{{ strtoupper(substr($pelanggan->name, 0, 2)) }}">
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

                    <div id="empty-search" class="empty-state" style="display:none; padding:40px 20px;">
                        <div class="empty-state__icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <div class="empty-state__text">Pelanggan tidak ditemukan</div>
                    </div>
                @endif
            </div>

        </div>

        {{-- Panel kanan: area chat --}}
        <div class="chat-message-panel" id="chat-panel-placeholder">
            <div class="empty-state" style="margin:auto; padding:60px 20px;">
                <div class="empty-state__icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="empty-state__title">Pilih percakapan</div>
                <div class="empty-state__sub">Klik nama pelanggan di sebelah kiri untuk membuka chat</div>
            </div>
        </div>

    </div>

</div>

{{-- Modal Chat — mobile fullscreen --}}
<div id="modal-chat" class="chat-modal">

    <div class="room-header">
        <button id="btn-tutup-chat" class="btn-icon" aria-label="Kembali">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
        </button>
        <div id="chat-avatar" class="room-avatar"></div>
        <div>
            <div id="chat-nama" class="room-title"></div>
            <div class="room-status">
                <div class="status-dot"></div> Pelanggan
            </div>
        </div>
        <div class="header-actions">
            <button class="btn-icon" aria-label="Telepon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
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
            <textarea id="chat-input" rows="1" placeholder="Tulis pesan..."></textarea>
        </div>
        <button id="btn-send" class="btn-send" aria-label="Kirim">
            <svg class="send-icon" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <line x1="22" y1="2" x2="11" y2="13"/>
                <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    window.adminUserId   = {{ Auth::id() }};
    window.chatPelanggan = @json($pelangganDenganPesan->pluck('id'));
</script>
@vite(['resources/js/admin/chat.js'])
@endpush

@endsection
