<x-guest-layout>
    <x-slot:title>Pesan {{ $vehicle->brand }} {{ $vehicle->model }}</x-slot:title>

    <div class="container" style="padding-top:32px;padding-bottom:60px;">

        {{-- Breadcrumb --}}
        <div style="display:flex;align-items:center;gap:6px;font-size:.8rem;color:var(--gray-500);margin-bottom:24px;">
            <a href="{{ route('cars.search') }}" style="color:var(--gray-500);">Kendaraan</a> ›
            <a href="{{ route('cars.show', $vehicle) }}" style="color:var(--gray-500);">{{ $vehicle->brand }}
                {{ $vehicle->model }}</a> ›
            <span>Pemesanan</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 380px;gap:40px;align-items:start;">

            {{-- ─── Form ─── --}}
            <div>
                <h2 style="margin-bottom:24px;">Detail Pemesanan</h2>

                @if($errors->any())
                <div class="alert alert-danger">
                    <div>
                        @foreach($errors->all() as $e)
                        <div>⚠️ {{ $e }}</div>
                        @endforeach
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('customer.bookings.store') }}" id="booking-form"
                    x-data="bookingForm()">
                    @csrf
                    <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                    {{-- Tanggal Sewa --}}
                    <div class="card" style="margin-bottom:20px;">
                        <div class="card-header">
                            <div class="card-title">📅 Tanggal Sewa</div>
                        </div>
                        <div class="card-body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Tanggal Mulai <span
                                            style="color:var(--danger);">*</span></label>
                                    <input type="date" name="start_date"
                                        class="form-input {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                        x-model="startDate" @change="calculate" min="{{ today()->format('Y-m-d') }}"
                                        value="{{ old('start_date') }}" required>
                                    @error('start_date') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Tanggal Selesai <span
                                            style="color:var(--danger);">*</span></label>
                                    <input type="date" name="end_date"
                                        class="form-input {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                        x-model="endDate" @change="calculate"
                                        :min="startDate || '{{ today()->addDay()->format('Y-m-d') }}'"
                                        value="{{ old('end_date') }}" required>
                                    @error('end_date') <div class="form-error">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div x-show="days > 0"
                                style="margin-top:12px;padding:10px 14px;background:var(--brand-50);border-radius:var(--radius-md);border:1px solid var(--brand-100);">
                                <span style="color:var(--brand-600);font-weight:600;font-size:.875rem;">
                                    ✅ Durasi sewa: <span x-text="days"></span> hari
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Lokasi & Catatan --}}
                    <div class="card" style="margin-bottom:20px;">
                        <div class="card-header">
                            <div class="card-title">📍 Lokasi & Catatan</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Lokasi Pengambilan</label>
                                <input type="text" name="pickup_location" class="form-input"
                                    placeholder="Cth: Bandara Soekarno-Hatta Terminal 2"
                                    value="{{ old('pickup_location') }}">
                                <div class="form-hint">Kosongkan jika sesuai dengan lokasi mitra.</div>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Catatan untuk Mitra</label>
                                <textarea name="notes" class="form-textarea"
                                    placeholder="Permintaan khusus, jam pengambilan, dsb.">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Konfirmasi Syarat --}}
                    <div class="card" style="margin-bottom:24px;">
                        <div class="card-body">
                            <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                                <input type="checkbox" id="agree-terms" required
                                    style="margin-top:3px;accent-color:var(--brand-400);">
                                <span style="font-size:.875rem;color:var(--gray-700);line-height:1.6;">
                                    Saya telah membaca dan menyetujui
                                    <a href="#" style="color:var(--brand-500);font-weight:600;">Syarat & Ketentuan</a>
                                    serta
                                    <a href="#" style="color:var(--brand-500);font-weight:600;">Kebijakan Privasi</a>
                                    RentWheels.
                                </span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        💳 Lanjut ke Pembayaran
                    </button>
                </form>
            </div>

            {{-- ─── Summary Card ─── --}}
            <div style="position:sticky;top:88px;">
                <div class="card">
                    <div style="position:relative;height:180px;background:var(--gray-100);overflow:hidden;">
                        <img src="{{ $vehicle->primary_photo_url }}" alt="{{ $vehicle->brand }}"
                            style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <div class="card-body">
                        <div
                            style="font-family:'Plus Jakarta Sans',sans-serif;font-weight:700;font-size:1.1rem;margin-bottom:4px;">
                            {{ $vehicle->brand }} {{ $vehicle->model }} {{ $vehicle->year }}
                        </div>
                        <div class="text-sm text-muted" style="margin-bottom:16px;">
                            {{ $vehicle->city }} · {{ Str::title($vehicle->transmission) }} · {{ $vehicle->capacity }}
                            penumpang
                        </div>

                        {{-- Pemilik --}}
                        @php $owner = \App\Models\User::getOwner(); @endphp
                        <div
                            style="display:flex;align-items:center;gap:8px;padding:10px 0;border-top:1px solid var(--gray-100);margin-bottom:12px;">
                            <img src="{{ $owner->avatar_url ?? asset('images/avatar-placeholder.png') }}"
                                style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                            <div>
                                <div class="text-sm fw-600">{{ $owner->name }}</div>
                                <div class="text-xs text-muted">Terverifikasi ✓</div>
                            </div>
                        </div>

                        {{-- Price breakdown (reactive) --}}
                        <div x-data="bookingForm()" style="font-size:.875rem;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span class="text-muted">{{ $vehicle->price_formatted }} × <span
                                        x-text="days || '?'"></span> hari</span>
                                <span class="fw-600"
                                    x-text="days > 0 ? formatRp(days * {{ $vehicle->price_per_day }}) : '—'"></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span class="text-muted">Biaya layanan</span>
                                <span class="fw-600"
                                    x-text="days > 0 ? formatRp(Math.round(days * {{ $vehicle->price_per_day }} * 0.05)) : '—'"></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:12px;">
                                <span class="text-muted">Deposit</span>
                                <span class="fw-600">{{ $vehicle->deposit_formatted }}</span>
                            </div>
                            <div
                                style="display:flex;justify-content:space-between;padding-top:12px;border-top:1.5px solid var(--gray-200);">
                                <span class="fw-700">Total</span>
                                <span class="fw-700" style="color:var(--brand-500);font-size:1rem;"
                                    x-text="days > 0 ? formatRp(grandTotal) : '—'"></span>
                            </div>
                        </div>

                        <div
                            style="margin-top:14px;padding:10px;background:var(--success-bg);border-radius:var(--radius-md);font-size:.75rem;color:var(--success);">
                            🛡️ Pembayaran dilindungi. Deposit dikembalikan setelah sewa selesai.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function bookingForm() {
        return {
            startDate: document.querySelector('[name=start_date]')?.value || '',
            endDate: document.querySelector('[name=end_date]')?.value || '',
            get days() {
                if (!this.startDate || !this.endDate) return 0;
                const diff = (new Date(this.endDate) - new Date(this.startDate)) / 86400000;
                return diff > 0 ? Math.round(diff) : 0;
            },
            get grandTotal() {
                const base = this.days * {
                    {
                        $vehicle - > price_per_day
                    }
                };
                return base + Math.round(base * 0.05) + {
                    {
                        $vehicle - > deposit
                    }
                };
            },
            formatRp(n) {
                return 'Rp ' + n.toLocaleString('id-ID');
            },
            calculate() {}
        }
    }
    </script>
    @endpush
</x-guest-layout>
