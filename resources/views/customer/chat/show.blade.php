<x-guest-layout>
    <x-slot:title>Chat — {{ $booking->booking_code }}</x-slot:title>

    <div class="container container-md" style="padding-top:32px;padding-bottom:48px;">

        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <a href="{{ route('customer.bookings.show', $booking) }}"
               class="btn btn-sm btn-secondary btn-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:1.05rem;margin-bottom:2px;">Chat dengan RentWheels</h1>
                <p class="text-xs text-muted">
                    {{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} ·
                    {{ $booking->booking_code }}
                </p>
            </div>
            <span class="badge badge-{{ $booking->status->value === 'dikonfirmasi' || $booking->status->value === 'aktif' ? 'active' : ($booking->status->isFinal() ? 'done' : 'pending') }}">
                {{ $booking->status->label() }}
            </span>
        </div>

        {{-- Chat Box --}}
        <div class="chat-container">

            {{-- Chat Header --}}
            <div class="chat-header">
                <div style="width:40px;height:40px;border-radius:50%;background:var(--brand-600);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <div style="font-weight:700;font-size:.9rem;">RentWheels Admin</div>
                    <div style="font-size:.75rem;color:var(--success);display:flex;align-items:center;gap:5px;">
                        <span style="width:7px;height:7px;background:var(--success);border-radius:50%;display:inline-block;"></span>
                        Online · Biasanya membalas dalam 5 menit
                    </div>
                </div>
                <div style="margin-left:auto;display:flex;gap:8px;">
                    <a href="{{ route('customer.bookings.show', $booking) }}"
                       class="btn btn-sm btn-secondary" style="gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Detail Pemesanan
                    </a>
                </div>
            </div>

            {{-- Messages --}}
            <div id="chat-messages" class="chat-messages">

                {{-- Info Header --}}
                <div class="chat-date-divider">
                    <span>Percakapan tentang pemesanan ini</span>
                </div>

                {{-- Booking Info Card --}}
                <div style="margin-bottom:16px;background:#fff;border:1px solid var(--gray-200);border-radius:var(--radius-md);padding:14px;max-width:320px;">
                    <div style="font-size:.72rem;font-weight:700;color:var(--gray-500);text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Info Pemesanan</div>
                    <div style="display:flex;flex-direction:column;gap:5px;">
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--gray-500);">Kendaraan</span>
                            <span style="font-weight:600;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--gray-500);">Tanggal</span>
                            <span style="font-weight:600;">{{ $booking->start_date->format('d M') }} – {{ $booking->end_date->format('d M Y') }}</span>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:.82rem;">
                            <span style="color:var(--gray-500);">Kode Booking</span>
                            <span style="font-weight:600;color:var(--brand-600);">{{ $booking->booking_code }}</span>
                        </div>
                    </div>
                </div>

                @php $lastDate = null; @endphp
                @forelse($messages as $msg)
                    @php
                        $isMine = $msg->sender_id === auth()->id();
                        $msgDate = $msg->created_at->toDateString();
                    @endphp

                    {{-- Date Divider --}}
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
                            <div class="chat-sender">RentWheels</div>
                            @endunless
                            <div class="chat-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                                {{-- Image attachment --}}
                                @if($msg->attachment_path)
                                <div style="margin-bottom:8px;">
                                    <img src="{{ Storage::url($msg->attachment_path) }}"
                                         style="max-width:200px;border-radius:var(--radius-sm);cursor:pointer;"
                                         onclick="window.open(this.src,'_blank')"
                                         alt="Lampiran">
                                </div>
                                @endif
                                {{ $msg->content }}
                            </div>
                            <div class="chat-time {{ $isMine ? '' : '' }}" style="{{ $isMine ? 'text-align:right;' : '' }}">
                                {{ $msg->created_at->format('H:i') }}
                                @if($isMine)
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $msg->read_at ? 'var(--brand-400)' : 'var(--gray-400)' }}" stroke-width="2.5" style="display:inline;vertical-align:middle;margin-left:2px;">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="chat-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <p>Belum ada pesan</p>
                        <p style="font-size:.8rem;">Tanya sesuatu kepada kami!</p>
                    </div>
                @endforelse
            </div>

            {{-- Input Area --}}
            @if(!$booking->status->isFinal())
            <div class="chat-input-area">
                {{-- Error --}}
                <div id="chat-error" style="display:none;margin-bottom:8px;" class="alert alert-danger"></div>

                {{-- Quick Replies --}}
                <div style="display:flex;gap:6px;overflow-x:auto;margin-bottom:10px;padding-bottom:2px;scrollbar-width:none;">
                    @foreach([
                        'Apakah kendaraan siap?',
                        'Lokasi pengambilan di mana?',
                        'Apakah bisa diantar?',
                        'Konfirmasi pembayaran saya',
                    ] as $quick)
                    <button type="button"
                            onclick="document.getElementById('chat-input').value='{{ $quick }}'"
                            style="flex-shrink:0;padding:5px 12px;border:1px solid var(--gray-200);border-radius:var(--radius-full);font-size:.75rem;background:#fff;cursor:pointer;white-space:nowrap;transition:all .15s;font-family:inherit;"
                            onmouseover="this.style.borderColor='var(--brand-400)';this.style.color='var(--brand-600)'"
                            onmouseout="this.style.borderColor='var(--gray-200)';this.style.color='inherit'">
                        {{ $quick }}
                    </button>
                    @endforeach
                </div>

                <form id="chat-form" class="chat-form">
                    @csrf
                    <textarea id="chat-input"
                              rows="1"
                              class="chat-textarea"
                              placeholder="Ketik pesan..."></textarea>
                    <button id="chat-send-btn" type="submit" class="chat-send-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/></svg>
                    </button>
                </form>
                <p style="font-size:.7rem;color:var(--gray-400);margin-top:6px;">Enter untuk kirim · Shift+Enter baris baru</p>
            </div>
            @else
            <div style="padding:16px;background:var(--gray-50);text-align:center;border-top:1px solid var(--gray-100);">
                <p style="font-size:.875rem;color:var(--gray-500);">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Chat tidak tersedia untuk pemesanan yang sudah selesai
                </p>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        initChat(
            {{ $booking->id }},
            {{ auth()->id() }},
            '{{ route('customer.chat.send', $booking) }}',
            '{{ csrf_token() }}'
        );
    });
    </script>
    @endpush
</x-guest-layout>
