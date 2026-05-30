<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
    <style>
        .form-label {
            display: block; font-size: 12px; font-weight: 700;
            color: var(--gray-600); margin-bottom: 6px;
            text-transform: uppercase; letter-spacing: .4px;
        }
        .form-input {
            width: 100%; padding: 12px 14px;
            border: 1.5px solid var(--gray-200); border-radius: var(--radius-sm);
            font-family: var(--font); font-size: 14px; color: var(--gray-900);
            background: #fff; transition: border-color .15s; box-sizing: border-box;
        }
        .form-input:focus {
            outline: none; border-color: var(--brand-400);
            box-shadow: 0 0 0 3px rgba(37,99,235,.08);
        }
        .form-input:disabled {
            background: var(--gray-50); color: var(--gray-400); cursor: not-allowed;
        }
        .form-input.error { border-color: #ef4444; }
        .field-error { font-size: 12px; color: #ef4444; margin-top: 5px; }
        .form-group { margin-bottom: 16px; }
        .section-card {
            background: #fff; border: 1px solid var(--gray-100);
            border-radius: var(--radius-md); padding: 20px;
            margin-bottom: 16px; box-shadow: var(--shadow-sm);
        }
        .section-title {
            font-size: 15px; font-weight: 700; color: var(--gray-900);
            margin-bottom: 4px;
        }
        .section-desc {
            font-size: 12px; color: var(--gray-500); margin-bottom: 20px;
        }
        .btn-primary {
            width: 100%; padding: 13px; background: var(--brand-400);
            color: #fff; border: none; border-radius: var(--radius-md);
            font-family: var(--font); font-size: 14px; font-weight: 700;
            cursor: pointer; transition: background .15s; margin-top: 4px;
        }
        .btn-primary:active { background: var(--brand-600); }
        .btn-danger {
            width: 100%; padding: 13px; background: #fff;
            color: #ef4444; border: 1.5px solid #fecaca;
            border-radius: var(--radius-md); font-family: var(--font);
            font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all .15s; margin-top: 4px;
        }
        .btn-danger:hover { background: #fef2f2; }
        .success-pill {
            display: inline-flex; align-items: center; gap: 5px;
            background: #f0fdf4; color: #16a34a;
            border: 1px solid #bbf7d0; border-radius: 20px;
            font-size: 12px; font-weight: 600; padding: 5px 12px;
            margin-top: 10px;
        }

        /* Modal hapus akun */
        .modal-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.5); z-index: 300;
            align-items: center; justify-content: center; padding: 20px;
        }
        .modal-overlay.open { display: flex; }
        .modal-box {
            background: #fff; border-radius: var(--radius-md);
            padding: 24px; width: 100%; max-width: 380px;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
        }
        .modal-title {
            font-size: 16px; font-weight: 700; color: var(--gray-900); margin-bottom: 8px;
        }
        .modal-desc {
            font-size: 13px; color: var(--gray-500); margin-bottom: 20px; line-height: 1.6;
        }
        .modal-actions { display: flex; gap: 10px; }
        .modal-actions .btn-cancel {
            flex: 1; padding: 12px; background: var(--gray-100);
            color: var(--gray-700); border: none; border-radius: var(--radius-sm);
            font-family: var(--font); font-size: 14px; font-weight: 600; cursor: pointer;
        }
        .modal-actions .btn-confirm-delete {
            flex: 1; padding: 12px; background: #ef4444;
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: var(--font); font-size: 14px; font-weight: 700; cursor: pointer;
        }
    </style>
</head>
<body>

<nav class="nav">
    <button onclick="history.back()"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Edit Profil</div>
    <div style="width:36px;"></div>
</nav>

<div class="content" style="padding:16px 20px 100px;">

    {{-- Avatar --}}
    <div style="display:flex;flex-direction:column;align-items:center;margin-bottom:24px;">
        <div style="width:72px;height:72px;border-radius:50%;background:var(--brand-100);
                    display:flex;align-items:center;justify-content:center;
                    font-size:26px;font-weight:800;color:var(--brand-600);margin-bottom:10px;">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div style="font-size:16px;font-weight:700;color:var(--gray-900);">{{ $user->name }}</div>
        <div style="font-size:13px;color:var(--gray-500);margin-top:2px;">{{ $user->email }}</div>
    </div>

    {{-- ── Section 1: Info Profil ── --}}
    <div class="section-card">
        <div class="section-title">👤 Informasi Profil</div>
        <div class="section-desc">Perbarui nama, email, dan nomor HP akun kamu</div>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="form-group">
                <label class="form-label" for="name">Nama Lengkap</label>
                <input id="name" name="name" type="text"
                       class="form-input {{ $errors->get('name') ? 'error' : '' }}"
                       value="{{ old('name', $user->name) }}"
                       required autocomplete="name">
                @if ($errors->get('name'))
                    <div class="field-error">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Alamat Email</label>
                <input id="email" name="email" type="email"
                       class="form-input {{ $errors->get('email') ? 'error' : '' }}"
                       value="{{ old('email', $user->email) }}"
                       required autocomplete="email">
                @if ($errors->get('email'))
                    <div class="field-error">{{ $errors->first('email') }}</div>
                @endif

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div style="margin-top:10px;padding:10px 12px;background:#fffbeb;border:1px solid #fef08a;border-radius:var(--radius-sm);">
                        <div style="font-size:12px;color:#a16207;margin-bottom:6px;">⚠️ Email belum terverifikasi</div>
                        <button form="send-verification"
                            style="font-size:12px;font-weight:600;color:var(--brand-400);background:none;border:none;cursor:pointer;padding:0;font-family:var(--font);">
                            Kirim ulang email verifikasi →
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <div style="font-size:12px;color:#16a34a;margin-top:6px;font-weight:600;">
                                ✅ Link verifikasi telah dikirim!
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="no_hp">Nomor HP</label>
                <input id="no_hp" name="no_hp" type="tel"
                       class="form-input"
                       value="{{ old('no_hp', $user->no_hp ?? '') }}"
                       placeholder="Contoh: 08123456789"
                       autocomplete="tel">
            </div>

            <button type="submit" class="btn-primary">Simpan Perubahan</button>

            @if (session('status') === 'profile-updated')
                <div class="success-pill">✅ Profil berhasil diperbarui</div>
            @endif
        </form>
    </div>

    {{-- ── Section 2: Ganti Password ── --}}
    <div class="section-card">
        <div class="section-title">🔒 Ganti Password</div>
        <div class="section-desc">Gunakan password yang panjang dan unik agar akun tetap aman</div>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="form-group">
                <label class="form-label" for="current_password">Password Saat Ini</label>
                <input id="current_password" name="current_password" type="password"
                       class="form-input {{ $errors->updatePassword->get('current_password') ? 'error' : '' }}"
                       placeholder="Masukkan password lama"
                       autocomplete="current-password">
                @if ($errors->updatePassword->get('current_password'))
                    <div class="field-error">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password Baru</label>
                <input id="password" name="password" type="password"
                       class="form-input {{ $errors->updatePassword->get('password') ? 'error' : '' }}"
                       placeholder="Min. 8 karakter"
                       autocomplete="new-password">
                @if ($errors->updatePassword->get('password'))
                    <div class="field-error">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       class="form-input"
                       placeholder="Ulangi password baru"
                       autocomplete="new-password">
            </div>

            <button type="submit" class="btn-primary">Perbarui Password</button>

            @if (session('status') === 'password-updated')
                <div class="success-pill">✅ Password berhasil diperbarui</div>
            @endif
        </form>
    </div>

    {{-- ── Section 3: Hapus Akun ── --}}
    <div class="section-card" style="border-color:#fecaca;">
        <div class="section-title" style="color:#ef4444;">⚠️ Hapus Akun</div>
        <div class="section-desc">
            Setelah akun dihapus, semua data akan hilang secara permanen dan tidak dapat dipulihkan.
        </div>
        <button type="button" class="btn-danger" onclick="bukaModalHapus()">
            Hapus Akun Saya
        </button>
    </div>

</div>

{{-- ── Modal Konfirmasi Hapus ── --}}
<div class="modal-overlay" id="modal-hapus">
    <div class="modal-box">
        <div style="font-size:36px;text-align:center;margin-bottom:12px;">🗑️</div>
        <div class="modal-title" style="text-align:center;">Hapus akun?</div>
        <div class="modal-desc" style="text-align:center;">
            Semua data pemesanan dan informasi akun akan dihapus permanen.
            Masukkan password untuk konfirmasi.
        </div>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group">
                <label class="form-label" for="delete_password">Password</label>
                <input id="delete_password" name="password" type="password"
                       class="form-input {{ $errors->userDeletion->get('password') ? 'error' : '' }}"
                       placeholder="Masukkan password kamu">
                @if ($errors->userDeletion->get('password'))
                    <div class="field-error">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="tutupModalHapus()">Batal</button>
                <button type="submit" class="btn-confirm-delete">Hapus</button>
            </div>
        </form>
    </div>
</div>

@include('users.partials.bottom-nav')

<script>
function bukaModalHapus() {
    document.getElementById('modal-hapus').classList.add('open');
}
function tutupModalHapus() {
    document.getElementById('modal-hapus').classList.remove('open');
}

// Auto-buka modal jika ada error validasi password hapus akun
@if ($errors->userDeletion->isNotEmpty())
    bukaModalHapus();
@endif
</script>

</body>
</html>