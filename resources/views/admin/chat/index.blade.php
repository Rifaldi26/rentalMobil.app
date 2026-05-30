<x-app-layout>
    <x-slot:title>Chat — Semua Percakapan</x-slot:title>

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
                    $unread = \App\Models\Message::where('booking_id', $cb->id)->where('sender_id', '!=', auth()->id())->whereNull('read_at')->count();
                @endphp
                <a href="{{ route('admin.chat.show', $cb) }}"
                   style="display:flex;gap:12px;padding:14px 16px;border-bottom:1px solid var(--gray-50);text-decoration:none;background:transparent;transition:background .15s;border-left:3px solid transparent;"
                   onmouseover="this.style.background='var(--gray-50)'"
                   onmouseout="this.style.background='transparent'">
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

        {{-- ── RIGHT: Placeholder ──────────────────────────── --}}
        <div class="card" style="display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;color:var(--gray-400);">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <p style="font-size:.95rem;font-weight:600;">Pilih percakapan</p>
            <p style="font-size:.82rem;">Klik salah satu percakapan di sebelah kiri untuk mulai membalas</p>
        </div>

    </div>
</x-app-layout>