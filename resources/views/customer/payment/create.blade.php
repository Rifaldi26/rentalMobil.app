<x-guest-layout>
    <x-slot:title>Pembayaran — {{ $booking->booking_code }}</x-slot:title>

    @push('styles')
    <style>
    .payment-layout { display:grid;grid-template-columns:1fr 380px;gap:28px; }
    .method-card {
        display:flex;align-items:center;gap:14px;padding:16px 18px;
        border:2px solid var(--gray-200);border-radius:var(--radius-lg);
        cursor:pointer;transition:all .2s;margin-bottom:10px;
    }
    .method-card:hover { border-color:var(--brand-300);background:var(--brand-50); }
    .method-card.selected { border-color:var(--brand-500);background:var(--brand-50); }
    .method-card input[type="radio"] { accent-color:var(--brand-600); }
    .method-logo { width:48px;height:28px;object-fit:contain;flex-shrink:0; }
    .step-indicator {
        display:flex;align-items:center;gap:0;margin-bottom:32px;
    }
    .step { display:flex;align-items:center;gap:8px;flex:1; }
    .step-dot {
        width:28px;height:28px;border-radius:50%;display:flex;align-items:center;
        justify-content:center;font-size:.75rem;font-weight:800;flex-shrink:0;
        font-family:'Sora',sans-serif;
    }
    .step-dot.done { background:var(--brand-500);color:#fff; }
    .step-dot.active { background:var(--brand-600);color:#fff;box-shadow:0 0 0 4px rgba(20,184,166,.2); }
    .step-dot.pending { background:var(--gray-200);color:var(--gray-400); }
    .step-label { font-size:.78rem;font-weight:600; }
    .step-line { flex:1;height:2px;background:var(--gray-200);margin:0 8px; }
    .step-line.done { background:var(--brand-400); }
    .upload-zone {
        border:2px dashed var(--gray-300);border-radius:var(--radius-lg);
        padding:32px 20px;text-align:center;cursor:pointer;transition:all .2s;
    }
    .upload-zone:hover,.upload-zone.dragover { border-color:var(--brand-400);background:var(--brand-50); }
    @media(max-width:768px) { .payment-layout { grid-template-columns:1fr; } }
    </style>
    @endpush

    <div class="container" style="padding-top:32px;padding-bottom:64px;">

        {{-- Step Indicator --}}
        <div class="step-indicator">
            <div class="step">
                <div class="step-dot done">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <span class="step-label" style="color:var(--brand-600);">Pemesanan</span>
            </div>
            <div class="step-line done"></div>
            <div class="step">
                <div class="step-dot active">2</div>
                <span class="step-label" style="color:var(--brand-700);">Pembayaran</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-dot pending">3</div>
                <span class="step-label" style="color:var(--gray-400);">Konfirmasi</span>
            </div>
        </div>

        <div class="payment-layout">

            {{-- ── Left: Payment Methods ───────────────── --}}
            <div>
                <div class="card" style="padding:24px;margin-bottom:20px;">
                    <h2 style="font-size:1.1rem;margin-bottom:4px;">Pilih Metode Pembayaran</h2>
                    <p class="text-sm text-muted" style="margin-bottom:20px;">Selesaikan pembayaran dalam <strong id="countdown" style="color:var(--danger);">23:59:00</strong></p>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
                        <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                    </div>
                    @endif

                    <form action="{{ route('customer.payments.store', $booking) }}" method="POST" id="payment-form" enctype="multipart/form-data">
                        @csrf

                        {{-- Transfer Bank --}}
                        <div style="margin-bottom:20px;">
                            <div class="form-label" style="margin-bottom:10px;font-size:.8rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.5px;font-weight:700;">Transfer Bank</div>

                            @foreach([
                                ['value'=>'bca','label'=>'Bank BCA','sub'=>'Virtual Account tersedia'],
                                ['value'=>'bni','label'=>'Bank BNI','sub'=>'Virtual Account tersedia'],
                                ['value'=>'mandiri','label'=>'Bank Mandiri','sub'=>'Virtual Account tersedia'],
                                ['value'=>'bri','label'=>'Bank BRI','sub'=>'Virtual Account tersedia'],
                            ] as $m)
                            <label class="method-card" for="method_{{ $m['value'] }}">
                                <input type="radio" name="payment_method" id="method_{{ $m['value'] }}"
                                       value="{{ $m['value'] }}" {{ old('payment_method') === $m['value'] ? 'checked' : '' }}
                                       onchange="showInstructions('{{ $m['value'] }}')">
                                <div style="width:48px;height:28px;background:var(--gray-100);border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="font-size:.65rem;font-weight:800;color:var(--gray-600);">{{ strtoupper($m['value']) }}</span>
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:700;font-size:.9rem;">{{ $m['label'] }}</div>
                                    <div style="font-size:.75rem;color:var(--gray-400);">{{ $m['sub'] }}</div>
                                </div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </label>
                            @endforeach
                        </div>

                        {{-- E-Wallet --}}
                        <div style="margin-bottom:20px;">
                            <div class="form-label" style="margin-bottom:10px;font-size:.8rem;color:var(--gray-400);text-transform:uppercase;letter-spacing:.5px;font-weight:700;">E-Wallet</div>

                            @foreach([
                                ['value'=>'gopay','label'=>'GoPay','sub'=>'QR Code / Link pembayaran'],
                                ['value'=>'ovo','label'=>'OVO','sub'=>'Masukkan nomor HP terdaftar'],
                                ['value'=>'dana','label'=>'DANA','sub'=>'QR Code / Link pembayaran'],
                            ] as $m)
                            <label class="method-card" for="method_{{ $m['value'] }}">
                                <input type="radio" name="payment_method" id="method_{{ $m['value'] }}"
                                       value="{{ $m['value'] }}" {{ old('payment_method') === $m['value'] ? 'checked' : '' }}
                                       onchange="showInstructions('{{ $m['value'] }}')">
                                <div style="width:48px;height:28px;background:var(--gray-100);border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="font-size:.65rem;font-weight:800;color:var(--gray-600);">{{ strtoupper($m['value']) }}</span>
                                </div>
                                <div style="flex:1;">
                                    <div style="font-weight:700;font-size:.9rem;">{{ $m['label'] }}</div>
                                    <div style="font-size:.75rem;color:var(--gray-400);">{{ $m['sub'] }}</div>
                                </div>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                            </label>
                            @endforeach
                        </div>

                        {{-- Payment Instructions --}}
                        <div id="payment-instructions" style="display:none;background:var(--info-bg);border:1px solid var(--info-border);border-radius:var(--radius-lg);padding:18px;margin-bottom:20px;">
                            <div style="font-weight:700;font-size:.9rem;color:var(--info);margin-bottom:10px;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Instruksi Transfer
                            </div>
                            <div style="font-size:.875rem;color:var(--gray-700);">
                                <div style="margin-bottom:8px;">Transfer ke rekening berikut:</div>
                                <div style="background:#fff;border-radius:var(--radius-md);padding:14px;font-family:'Sora',sans-serif;">
                                    <div style="font-size:.75rem;color:var(--gray-400);margin-bottom:2px;">Nomor Rekening</div>
                                    <div style="font-size:1.1rem;font-weight:800;letter-spacing:1px;margin-bottom:8px;" id="va-number">1234-5678-9012</div>
                                    <div style="font-size:.75rem;color:var(--gray-400);margin-bottom:2px;">Atas Nama</div>
                                    <div style="font-weight:700;">RentWheels Indonesia</div>
                                </div>
                                <div style="margin-top:10px;font-size:.8rem;color:var(--gray-500);">
                                    Pastikan jumlah transfer <strong>tepat sama</strong> dengan nominal yang tertera
                                </div>
                            </div>
                        </div>

                        {{-- Upload Bukti --}}
                        <div class="form-group">
                            <label class="form-label">Upload Bukti Pembayaran</label>
                            <div class="upload-zone" id="upload-zone"
                                 onclick="document.getElementById('proof_file').click()"
                                 ondragover="event.preventDefault();this.classList.add('dragover')"
                                 ondragleave="this.classList.remove('dragover')"
                                 ondrop="handleDrop(event)">
                                <div id="upload-placeholder">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin:0 auto 10px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    <div style="font-weight:600;color:var(--gray-600);margin-bottom:4px;">Klik atau seret file ke sini</div>
                                    <div style="font-size:.8rem;color:var(--gray-400);">PNG, JPG, PDF · Maks 5MB</div>
                                </div>
                                <div id="upload-preview" style="display:none;">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" style="margin:0 auto 8px;"><polyline points="20 6 9 17 4 12"/></svg>
                                    <div id="upload-filename" style="font-weight:700;color:var(--gray-800);font-size:.9rem;"></div>
                                    <div style="font-size:.78rem;color:var(--gray-400);margin-top:4px;">Klik untuk ganti file</div>
                                </div>
                            </div>
                            <input type="file" id="proof_file" name="proof_file" accept="image/*,.pdf"
                                   style="display:none;" onchange="previewFile(this)" required>
                            @error('proof_file')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div class="form-group">
                            <label class="form-label">Catatan Pembayaran <span class="text-muted">(opsional)</span></label>
                            <input type="text" name="notes" class="form-input"
                                   placeholder="Nama pengirim, bank, dll."
                                   value="{{ old('notes') }}">
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Right: Order Summary ────────────────── --}}
            <div>
                <div class="card" style="padding:20px;margin-bottom:14px;">
                    <div style="font-weight:800;font-family:'Sora',sans-serif;font-size:.9rem;margin-bottom:16px;color:var(--gray-700);">RINGKASAN PEMESANAN</div>

                    {{-- Vehicle --}}
                    <div style="display:flex;gap:12px;margin-bottom:16px;">
                        <img src="{{ $booking->vehicle->primary_photo_url }}"
                             style="width:72px;height:52px;object-fit:cover;border-radius:var(--radius-md);flex-shrink:0;"
                             alt="{{ $booking->vehicle->brand }}">
                        <div>
                            <div style="font-weight:700;font-size:.9rem;">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}</div>
                            <div style="font-size:.8rem;color:var(--gray-400);">{{ $booking->vehicle->year }} · {{ $booking->vehicle->license_plate }}</div>
                            <div style="font-size:.78rem;color:var(--brand-600);font-weight:600;margin-top:2px;">{{ $booking->vehicle->category->label() }}</div>
                        </div>
                    </div>

                    {{-- Booking Code --}}
                    <div style="background:var(--gray-50);border-radius:var(--radius-md);padding:12px 14px;margin-bottom:14px;">
                        <div style="font-size:.75rem;color:var(--gray-400);margin-bottom:2px;">Kode Pemesanan</div>
                        <div style="font-family:'Sora',sans-serif;font-weight:800;font-size:1rem;letter-spacing:.5px;color:var(--gray-900);">{{ $booking->booking_code }}</div>
                    </div>

                    {{-- Detail --}}
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
                        @foreach([
                            ['label'=>'Tanggal Mulai','value'=>$booking->start_date->format('d M Y')],
                            ['label'=>'Tanggal Selesai','value'=>$booking->end_date->format('d M Y')],
                            ['label'=>'Durasi','value'=>$booking->duration_days.' hari'],
                            ['label'=>'Sopir','value'=>$booking->with_driver ? 'Ya' : 'Tidak'],
                        ] as $d)
                        <div style="display:flex;justify-content:space-between;font-size:.85rem;">
                            <span style="color:var(--gray-500);">{{ $d['label'] }}</span>
                            <span class="fw-600">{{ $d['value'] }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div style="height:1px;background:var(--gray-100);margin-bottom:14px;"></div>

                    {{-- Pricing --}}
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        <div style="display:flex;justify-content:space-between;font-size:.85rem;">
                            <span style="color:var(--gray-500);">Sewa kendaraan</span>
                            <span class="fw-600">Rp {{ number_format($booking->vehicle->price_per_day * $booking->duration_days, 0, ',', '.') }}</span>
                        </div>
                        @if($booking->with_driver)
                        <div style="display:flex;justify-content:space-between;font-size:.85rem;">
                            <span style="color:var(--gray-500);">Biaya sopir</span>
                            <span class="fw-600">Rp {{ number_format(($booking->vehicle->driver_price_per_day ?? 150000) * $booking->duration_days, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div style="height:1px;background:var(--gray-100);margin:4px 0;"></div>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span class="fw-700">Total Pembayaran</span>
                            <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem;color:var(--brand-600);">
                                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Help --}}
                <div class="card" style="padding:16px 18px;">
                    <div style="font-weight:700;font-size:.875rem;margin-bottom:10px;">Butuh Bantuan?</div>
                    <a href="{{ route('customer.chat.show', $booking) }}"
                       class="btn btn-secondary" style="width:100%;justify-content:center;font-size:.875rem;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Chat dengan Admin
                    </a>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
    function showInstructions(method) {
        const banks = { bca: '1234-5678-9012', bni: '9988-7766-5544', mandiri: '1400-1234-5678', bri: '0123-0109-9999' };
        const inst = document.getElementById('payment-instructions');
        if (banks[method]) {
            inst.style.display = 'block';
            document.getElementById('va-number').textContent = banks[method];
        } else {
            inst.style.display = 'none';
        }
    }

    function previewFile(input) {
        if (!input.files.length) return;
        const f = input.files[0];
        document.getElementById('upload-placeholder').style.display = 'none';
        document.getElementById('upload-preview').style.display = 'block';
        document.getElementById('upload-filename').textContent = f.name;
    }

    function handleDrop(e) {
        e.preventDefault();
        document.getElementById('upload-zone').classList.remove('dragover');
        const dt = new DataTransfer();
        dt.items.add(e.dataTransfer.files[0]);
        const input = document.getElementById('proof_file');
        input.files = dt.files;
        previewFile(input);
    }

    // Countdown timer (24 hours from booking creation)
    const expiry = new Date({{ $booking->created_at->addHours(24)->timestamp }} * 1000);
    function updateCountdown() {
        const diff = expiry - Date.now();
        if (diff <= 0) { document.getElementById('countdown').textContent = 'Waktu habis'; return; }
        const h = Math.floor(diff / 3600000).toString().padStart(2,'0');
        const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2,'0');
        const s = Math.floor((diff % 60000) / 1000).toString().padStart(2,'0');
        document.getElementById('countdown').textContent = `${h}:${m}:${s}`;
        setTimeout(updateCountdown, 1000);
    }
    updateCountdown();
    </script>
    @endpush

</x-guest-layout>
