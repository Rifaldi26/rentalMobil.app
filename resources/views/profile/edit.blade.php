<x-guest-layout>
    <x-slot:title>Edit Profil</x-slot:title>

    <div class="container" style="padding-top:32px;padding-bottom:60px;max-width:680px;">
        <h2 style="margin-bottom:24px;">Profil Saya</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $e) <div>⚠️ {{ $e }}</div> @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}"
              enctype="multipart/form-data"
              x-data="{ previewUrl: null }">
            @csrf @method('PATCH')

            {{-- Avatar --}}
            <div class="card" style="margin-bottom:20px;">
                <div class="card-body" style="display:flex;align-items:center;gap:20px;">
                    <div style="position:relative;">
                        <img :src="previewUrl || '{{ $user->avatar_url }}'"
                             style="width:80px;height:80px;border-radius:50%;object-fit:cover;border:3px solid var(--gray-200);"
                             id="avatar-preview">
                        <label style="position:absolute;bottom:-4px;right:-4px;width:28px;height:28px;background:var(--brand-400);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid #fff;">
                            <span style="color:#fff;font-size:.75rem;">✏️</span>
                            <input type="file" name="avatar" accept="image/*" style="display:none;"
                                   @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                        </label>
                    </div>
                    <div>
                        <div class="fw-700" style="font-size:1.1rem;">{{ $user->name }}</div>
                        <div class="text-muted text-sm">{{ $user->email }}</div>
                        <div class="text-xs" style="margin-top:4px;">
                            <span class="badge
                                @if($user->isAdmin()) badge-confirmed
                                @elseif($user->isPartner()) badge-active
                                @else badge-pending @endif">
                                {{ $user->role->label() }}
                            </span>
                            @if($user->email_verified_at)
                                <span class="badge badge-active" style="margin-left:4px;">✅ Email Terverifikasi</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Dasar --}}
            <div class="card" style="margin-bottom:20px;">
                <div class="card-header"><div class="card-title">👤 Informasi Dasar</div></div>
                <div class="card-body">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group" style="grid-column:span 2;">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-input"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. HP</label>
                            <input type="tel" name="no_hp" class="form-input"
                                   value="{{ old('no_hp', $user->no_hp) }}"
                                   placeholder="081234567890">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ganti Password --}}
            <div class="card" style="margin-bottom:24px;" x-data="{ changePass: false }">
                <div class="card-header">
                    <div class="card-title">🔒 Kata Sandi</div>
                    <button type="button" @click="changePass = !changePass"
                            class="btn btn-sm btn-secondary" x-text="changePass ? 'Batal' : 'Ganti Password'">
                    </button>
                </div>
                <div class="card-body" x-show="changePass" x-transition>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-input"
                                   placeholder="Min. 8 karakter" autocomplete="new-password">
                            @error('password') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-input"
                                   placeholder="Ulangi password baru">
                        </div>
                    </div>
                    <div class="alert alert-info" style="font-size:.8rem;margin-bottom:0;">
                        ℹ️ Kosongkan jika tidak ingin mengganti password.
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-primary btn-lg" style="flex:1;">
                    💾 Simpan Perubahan
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg">Batal</a>
            </div>
        </form>

        {{-- Hapus Akun --}}
        <div class="card" style="margin-top:32px;border-color:var(--danger);" x-data="{ confirm: false }">
            <div class="card-body">
                <h4 style="color:var(--danger);margin-bottom:8px;">⚠️ Hapus Akun</h4>
                <p class="text-sm text-muted" style="margin-bottom:16px;">
                    Setelah akun dihapus, semua data tidak dapat dipulihkan. Pastikan Anda sudah mengunduh semua data penting.
                </p>
                <button type="button" @click="confirm = true"
                        class="btn btn-sm btn-danger">Hapus Akun Saya</button>

                <div x-show="confirm" x-transition style="margin-top:16px;padding:16px;background:var(--danger-bg);border-radius:var(--radius-md);">
                    <p class="text-sm fw-600" style="margin-bottom:12px;color:var(--danger);">
                        Masukkan password untuk konfirmasi penghapusan akun:
                    </p>
                    <form method="POST" action="{{ route('profile.destroy') }}"
                          style="display:flex;gap:8px;align-items:flex-end;">
                        @csrf @method('DELETE')
                        <div style="flex:1;">
                            <input type="password" name="password" class="form-input"
                                   placeholder="Password Anda" required>
                            @error('password', 'userDeletion') <div class="form-error">{{ $message }}</div> @enderror
                        </div>
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Yakin ingin menghapus akun secara permanen?')">
                            Konfirmasi Hapus
                        </button>
                        <button type="button" @click="confirm = false" class="btn btn-secondary">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
