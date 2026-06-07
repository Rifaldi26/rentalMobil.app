@extends('layouts.admin')

@section('title', 'Edit Profil')
@section('page-title', 'Edit Profil')
@section('page-subtitle', 'Kelola informasi akun dan keamanan Anda')

@section('content')
<div class="admin-content">
    <div class="profile-grid">

        {{-- ── Kolom Kiri: Avatar Card ── --}}
        <div class="profile-avatar-card">
            <div class="profile-avatar-circle">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <div class="profile-avatar-name">{{ $user->name }}</div>
            <div class="profile-avatar-email">{{ $user->email }}</div>
            <div class="profile-avatar-role">
                {{ $user->role === 'admin' ? '👑 Admin' : '👤 Pelanggan' }}
            </div>
        </div>

        {{-- ── Kolom Kanan: Form Cards ── --}}
        <div>

            {{-- Section 1: Informasi Profil --}}
            <div class="profile-section-card">
                <div class="profile-section-title">Informasi Profil</div>
                <div class="profile-section-desc">Perbarui nama, email, dan nomor HP akun Anda</div>

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
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="no_hp">Nomor HP</label>
                        <input id="no_hp" name="no_hp" type="tel"
                            class="form-input"
                            value="{{ old('no_hp', $user->no_hp ?? '') }}"
                            placeholder="Contoh: 08123456789"
                            autocomplete="tel">
                    </div>

                    <div class="profile-form-actions">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        @if (session('status') === 'profile-updated')
                            <span class="profile-success-pill">✓ Berhasil diperbarui</span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Section 2: Ganti Password --}}
            <div class="profile-section-card">
                <div class="profile-section-title">🔒 Ganti Password</div>
                <div class="profile-section-desc">Gunakan password yang panjang dan unik agar akun tetap aman</div>

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

                    <div class="profile-form-actions">
                        <button type="submit" class="btn btn-primary">Perbarui Password</button>
                        @if (session('status') === 'password-updated')
                            <span class="profile-success-pill">✅ Password diperbarui</span>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Section 3: Hapus Akun --}}
            <div class="profile-section-card profile-section-card--danger">
                <div class="profile-section-title profile-section-title--danger">⚠️ Hapus Akun</div>
                <div class="profile-section-desc">
                    Setelah akun dihapus, semua data akan hilang secara permanen dan tidak dapat dipulihkan.
                </div>
                <button type="button" class="btn btn-danger-outline" onclick="bukaModalHapus()">
                    Hapus Akun Saya
                </button>
            </div>

        </div>{{-- end kolom kanan --}}
    </div>{{-- end profile-grid --}}
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="profile-delete-modal" id="modal-hapus">
    <div class="profile-delete-modal__box">
        <div class="profile-delete-modal__icon">🗑️</div>
        <div class="profile-delete-modal__title">Hapus akun?</div>
        <div class="profile-delete-modal__desc">
            Semua data pemesanan dan informasi akun akan dihapus permanen.
            Masukkan password untuk konfirmasi.
        </div>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group" style="margin-top:16px;">
                <label class="form-label" for="delete_password">Password</label>
                <input id="delete_password" name="password" type="password"
                       class="form-input {{ $errors->userDeletion->get('password') ? 'error' : '' }}"
                       placeholder="Masukkan password kamu">
                @if ($errors->userDeletion->get('password'))
                    <div class="field-error">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>

            <div class="profile-delete-modal__actions">
                <button type="button" class="btn-cancel" onclick="tutupModalHapus()">Batal</button>
                <button type="submit" class="btn-confirm">Hapus</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/admin/profile.js'])
@if ($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', () => bukaModalHapus());
</script>
@endif
@endpush