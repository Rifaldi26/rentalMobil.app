<x-app-layout>
    <x-slot:title>Chat — {{ $booking->booking_code }}</x-slot:title>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:20px;height:calc(100vh - var(--topbar-height) - 56px);">

        {{-- ── LEFT: Conversation List ─────────────────────── --}}
        <div class="card" style="overflow:hidden;display:flex;flex-direction:column;">
            <div class="card-header">
                <div class="card-header-title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:middle;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Pesan Masuk
                </div>
                @php $totalUnread = \App\Models\Message::where('sender_id', '!=', auth()->id())->whereNull('read_at')->count(); @endphp
                @if($totalUnread > 0)
                    <span class="badge-dot">{{ $totalUnread }}</span>
                @endif
            </div>

            {{-- Search --}}
            <div style="padding:12px;border-bottom:1px solid var(--gray-100);">
                <div class="input-icon">
                    <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" class="form-input" placeholder="Cari percakapan..." style="padding-left:36px;font-size:.85rem;">
                </div>
            </div>

            {{-- Conversation List --}}
            <div style="flex:1;overflow-y:auto;">
                @php
                    $chatBookings = \App\Models\Booking::with(['user','vehicle','messages' => fn($q) => $q->latest()->limit(1)])
                        ->whereHas('messages')
                        ->orderByDesc(
                            \App\Models\Message::select('created_at')
                                ->whereColumn('booking_id', 'bookings.id')
                                ->orderByDesc('created_at')
                                ->limit(1)
                        )
                        ->take(20)->get();
                @endphp
                @forelse($chatBookings as $cb)
                @php
                    $lastMsg = $cb->messages->first();
                    $isActive = $cb->id === $booking->id;
                    $unread = \App\Models\Message::where('booking_id', $cb->id)->where('sender_id', '!=', auth()->id())->whereNull('read_at')->count();
                @endphp
                <a href="{{ route('admin.chat.show', $cb) }}"
                   style="display:flex;gap:12px;padding:14px 16px;border-bottom:1px solid var(--gray-50);text-decoration:none;background:{{ $isActive ? 'var(--brand-50)' : 'transparent' }};transition:background .15s;border-left:3px solid {{ $isActive ? 'var(--brand-600)' : 'transparent' }};"
                   onmouseover="if(!{{ $isActive ? 'true' : 'false' }})this.style.background='var(--gray-50)'"
                   onmouseout="if(!{{ $isActive ? 'true' : 'false' }})this.style.background='transparent'">
                    <img src="{{ $cb->user->avatar_url }}" class="avatar avatar-md" alt="" style="flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:3px;">
                            <span style="font-size:.85rem;font-weight:700;color:var(--gray-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">{{ $cb->user->name }}</span>
                            <span style="font-size:.7rem;color:var(--gray-400);flex-shrink:0;">{{ $lastMsg?->created_at->shortRelativeDiffForHumans() }}</span>
                        </div>
                        <div style="font-size:.75rem;color:var(--gray-500);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ Str::limit($lastMsg?->content ?? 'Belum ada pesan', 40) }}
                        </div>
                        <div style="font-size:.7rem;color:var(--gray-400);margin-top:2px;">{{ $cb->vehicle->brand }} {{ $cb->vehicle->model }}</div>
                    </div>
                    @if($unread > 0)
                    <span class="badge-dot" style="flex-shrink:0;align-self:center;">{{ $unread }}</span>
                    @endif
                </a>
                @empty
                <div class="empty-state" style="padding:40px 16px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <p>Belum ada percakapan</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ── RIGHT: Chat Window ──────────────────────────── --}}
        <div class="chat-container" style="height:100%;">
            {{-- Chat Header --}}
            <div class="chat-header">
                <img src="{{ $booking->user->avatar_url }}" class="avatar avatar-md" alt="">
                <div>
                    <div style="font-weight:700;font-size:.95rem;">{{ $booking->user->name }}</div>
                    <div style="font-size:.75rem;color:var(--gray-500);">
                        {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} ·
                        {{ $booking->booking_code }}
                    </div>
                </div>
                <div style="margin-left:auto;display:flex;align-items:center;gap:8px;">
                    <span class="badge badge-{{ ['pending'=>'pending','dikonfirmasi'=>'confirmed','aktif'=>'active','selesai'=>'done','dibatalkan'=>'cancelled'][$booking->status->value] ?? 'pending' }}">
                        {{ $booking->status->label() }}
                    </span>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-secondary btn-icon"
                       title="Lihat semua pemesanan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                </div>
            </div>

            {{-- Messages --}}
            <div id="chat-messages" class="chat-messages">
                <div class="chat-date-divider">
                    <span>Percakapan pemesanan {{ $booking->booking_code }}</span>
                </div>

                @php $lastDate = null; @endphp
                @forelse($messages as $msg)
                @php
                    $isMine = $msg->sender_id === auth()->id();
                    $msgDate = $msg->created_at->toDateString();
                @endphp
                @if($lastDate !== $msgDate)
                <div class="chat-date-divider">
                    <span>{{ $msg->created_at->isToday() ? 'Hari ini' : ($msg->created_at->isYesterday() ? 'Kemarin' : $msg->created_at->format('d M Y')) }}</span>
                </div>
                @php $lastDate = $msgDate; @endphp
                @endif

                <div class="chat-bubble-wrapper {{ $isMine ? 'mine' : 'theirs' }}"
                     data-message-id="{{ $msg->id }}">
                    <div>
                        @unless($isMine)
                        <div class="chat-sender">{{ $msg->sender->name }}</div>
                        @endunless
                        @if($msg->attachment_path)
                        <div style="margin-bottom:6px;">
                            <img src="{{ Storage::url($msg->attachment_path) }}"
                                 style="max-width:220px;border-radius:var(--radius-md);cursor:pointer;"
                                 onclick="window.open(this.src,'_blank')">
                        </div>
                        @endif
                        <div class="chat-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                            {{ $msg->content }}
                        </div>
                        <div class="chat-time" style="{{ $isMine ? 'text-align:right;' : '' }}">
                            {{ $msg->created_at->format('H:i') }}
                            @if($isMine)
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $msg->read_at ? 'var(--brand-400)' : 'var(--gray-400)' }}" stroke-width="2.5" style="display:inline;vertical-align:middle;margin-left:2px;"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="chat-empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <p>Belum ada pesan</p>
                    <p style="font-size:.8rem;">Mulai percakapan dengan pelanggan</p>
                </div>
                @endforelse
            </div>

            {{-- Input --}}
            @if(!$booking->status->isFinal())
            <div class="chat-input-area">
                <div id="chat-error" style="display:none;margin-bottom:8px;" class="alert alert-danger"></div>

                {{-- Template Quick Replies --}}
                <div style="display:flex;gap:6px;overflow-x:auto;margin-bottom:10px;padding-bottom:2px;scrollbar-width:none;">
                    @foreach([
                        'Pemesanan Anda telah kami konfirmasi ✅',
                        'Kendaraan sudah siap untuk diambil',
                        'Silakan hubungi kami jika ada kendala',
                        'Terima kasih telah menggunakan RentWheels!',
                    ] as $tpl)
                    <button type="button"
                            onclick="document.getElementById('chat-input').value='{{ $tpl }}'"
                            style="flex-shrink:0;padding:5px 12px;border:1px solid var(--gray-200);border-radius:var(--radius-full);font-size:.75rem;background:#fff;cursor:pointer;white-space:nowrap;transition:all .15s;font-family:inherit;"
                            onmouseover="this.style.borderColor='var(--brand-400)';this.style.color='var(--brand-600)'"
                            onmouseout="this.style.borderColor='var(--gray-200)';this.style.color='inherit'">
                        {{ Str::limit($tpl, 30) }}
                    </button>
                    @endforeach
                </div>

                <form id="chat-form" class="chat-form">
                    @csrf
                    <textarea id="chat-input"
                              rows="1"
                              class="chat-textarea"
                              placeholder="Tulis pesan kepada pelanggan..."></textarea>
                    <button id="chat-send-btn" type="submit" class="chat-send-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/></svg>
                    </button>
                </form>
                <p style="font-size:.7rem;color:var(--gray-400);margin-top:6px;">Enter untuk kirim · Shift+Enter baris baru</p>
            </div>
            @else
            <div style="padding:16px;background:var(--gray-50);text-align:center;border-top:1px solid var(--gray-100);">
                <p style="font-size:.875rem;color:var(--gray-500);">Pemesanan selesai. Chat tidak tersedia.</p>
            </div>
            @endif
        </div>
    </div>

    @push('styles')
    <style>
    @media (max-width: 1024px) {
        .chat-layout { grid-template-columns: 1fr !important; }
        .chat-conv-list { display: none; }
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        initChat(
            {{ $booking->id }},
            {{ auth()->id() }},
            '{{ route('admin.chat.send', $booking) }}',
            '{{ csrf_token() }}'
        );
    });
    </script>
    @endpush
</x-app-layout>
