<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Masuk — Rental Mobil</title>
    @vite('resources/css/login.css')
</head>
<body>

{{-- ─── Hero ─── --}}
<div class="auth-hero">
    <div class="auth-brand">Rental<span>Mobil</span></div>
    <div class="auth-tagline">Sewa mobil mudah, aman & terpercaya</div>
</div>

{{-- ─── Card ─── --}}
<div class="auth-card">

    {{-- Tabs --}}
    <div class="tab-row">
        <button class="tab-btn active" id="tab-login"    onclick="showTab('login')">Masuk</button>
        <button class="tab-btn"        id="tab-register" onclick="showTab('register')">Daftar</button>
    </div>

    {{-- ════ LOGIN FORM ════ --}}
    <form id="form-login" method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Session Error --}}
        @if (session('status'))
            <div class="toast show success" style="position:relative;transform:none;margin-bottom:16px;text-align:center;">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="toast show error" style="position:relative;transform:none;margin-bottom:16px;text-align:center;">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="login-email">
                Email <span class="req">*</span>
            </label>
            <input
                class="form-input @error('email') error @enderror"
                type="email"
                id="login-email"
                name="email"
                placeholder="email@contoh.com"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus
                required
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="pw-login">
                Kata Sandi <span class="req">*</span>
            </label>
            <div class="input-wrap">
                <input
                    class="form-input @error('password') error @enderror"
                    type="password"
                    id="pw-login"
                    name="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                >
                <button type="button" class="input-icon" onclick="togglePw('pw-login', this)">👁️</button>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror

            {{-- Remember Me --}}
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;">
                <label style="font-size:13px;color:var(--gray-500);display:flex;align-items:center;gap:6px;cursor:pointer;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link" style="margin-top:0;">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" id="btn-login">
            <div class="spinner" id="spin-login"></div>
            Masuk
        </button>

        {{-- Google --}}
        <button type="button" class="btn-google" onclick="handleGoogleAuth()">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Lanjutkan dengan Google
        </button>

        {{-- Demo Accounts --}}
        <div class="divider">Akun Demo</div>
        <div class="demo-grid">
            <button type="button" class="btn-demo"
                onclick="fillDemo('pelanggan@demo.com','Customer@1234')">
                👤 Pelanggan
            </button>
            <button type="button" class="btn-demo"
                onclick="fillDemo('admin@demo.com','Admin@1234')">
                🔑 Admin
            </button>
        </div>
    </form>

    {{-- ════ REGISTER FORM ════ --}}
    <form id="form-register" method="POST" action="{{ route('register') }}" style="display:none;">
        @csrf

        {{-- Role Selection --}}
        <div class="form-group">
            <label class="form-label">Saya ingin <span class="req">*</span></label>
            <div class="role-group">
                <label class="role-option selected" onclick="selectRole('customer', this)">
                    <input type="radio" class="role-radio" name="role" value="customer" checked>
                    <div class="role-icon">🚗</div>
                    <div class="role-name">Menyewa</div>
                    <div class="role-desc">Pelanggan</div>
                </label>
                <label class="role-option" onclick="selectRole('partner', this)">
                    <input type="radio" class="role-radio" name="role" value="partner">
                    <div class="role-icon">🤝</div>
                    <div class="role-name">Menyewakan</div>
                    <div class="role-desc">Menjadi Mitra</div>
                </label>
            </div>
        </div>

        {{-- Nama --}}
        <div class="form-group">
            <label class="form-label" for="reg-name">
                Nama Lengkap <span class="req">*</span>
            </label>
            <input
                class="form-input @error('name') error @enderror"
                type="text"
                id="reg-name"
                name="name"
                placeholder="Nama sesuai KTP"
                value="{{ old('name') }}"
                autocomplete="name"
                required
            >
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label class="form-label" for="reg-email">
                Email <span class="req">*</span>
            </label>
            <input
                class="form-input @error('email') error @enderror"
                type="email"
                id="reg-email"
                name="email"
                placeholder="email@contoh.com"
                value="{{ old('email') }}"
                autocomplete="email"
                required
            >
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- No HP --}}
        <div class="form-group">
            <label class="form-label" for="reg-phone">
                Nomor HP <span class="req">*</span>
            </label>
            <input
                class="form-input @error('phone') error @enderror"
                type="tel"
                id="reg-phone"
                name="phone"
                placeholder="08xxxxxxxxxx"
                value="{{ old('phone') }}"
                autocomplete="tel"
                required
            >
            @error('phone')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label class="form-label" for="pw-reg">
                Kata Sandi <span class="req">*</span>
            </label>
            <div class="input-wrap">
                <input
                    class="form-input @error('password') error @enderror"
                    type="password"
                    id="pw-reg"
                    name="password"
                    placeholder="Min. 8 karakter"
                    required
                >
                <button type="button" class="input-icon" onclick="togglePw('pw-reg', this)">👁️</button>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" id="btn-register">
            <div class="spinner" id="spin-register"></div>
            Buat Akun
        </button>

        {{-- Google --}}
        <button type="button" class="btn-google" onclick="handleGoogleAuth()">
            <svg width="18" height="18" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Daftar dengan Google
        </button>

        <p class="terms">
            Dengan mendaftar Anda menyetujui
            <a href="#">Syarat & Ketentuan</a> dan
            <a href="#">Kebijakan Privasi</a>.
        </p>
    </form>

</div>{{-- end .auth-card --}}

<div class="toast" id="toast"></div>

<script>
let selectedRole = 'customer';
let toastTimer;

// ─── Tab Switch ───────────────────────────────────────────
function showTab(tab) {
    const isLogin = tab === 'login';
    document.getElementById('tab-login').classList.toggle('active', isLogin);
    document.getElementById('tab-register').classList.toggle('active', !isLogin);
    document.getElementById('form-login').style.display    = isLogin ? '' : 'none';
    document.getElementById('form-register').style.display = isLogin ? 'none' : '';
}

// ─── Role Selection ───────────────────────────────────────
function selectRole(role, el) {
    selectedRole = role;
    document.querySelectorAll('.role-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
}

// ─── Toggle Password ──────────────────────────────────────
function togglePw(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.textContent = isText ? '👁️' : '🙈';
}

// ─── Fill Demo Account ────────────────────────────────────
function fillDemo(email, pass) {
    document.querySelector('#form-login [name="email"]').value    = email;
    document.querySelector('#form-login [name="password"]').value = pass;
    showToast('✅ Akun demo diisi — klik Masuk');
}

// ─── Google Auth ──────────────────────────────────────────
function handleGoogleAuth() {
    showToast('🔄 Mengarahkan ke Google...');
    setTimeout(() => {
        window.location.href = '{{ url("/auth/google") }}';
    }, 800);
}

// ─── Loading State ────────────────────────────────────────
function setLoading(btnId, spinId, loading) {
    const btn  = document.getElementById(btnId);
    const spin = document.getElementById(spinId);
    btn.disabled = loading;
    spin.style.display = loading ? 'block' : 'none';
}

// ─── Toast ────────────────────────────────────────────────
function showToast(msg, type = '') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = `toast ${type} show`;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => t.classList.remove('show'), 3000);
}

// ─── Loading on Submit ────────────────────────────────────
document.getElementById('form-login').addEventListener('submit', function () {
    setLoading('btn-login', 'spin-login', true);
});

document.getElementById('form-register').addEventListener('submit', function () {
    setLoading('btn-register', 'spin-register', true);
});

// ─── Jika ada error dari Laravel, tampilkan tab yang tepat ───
@if ($errors->hasBag('default') || $errors->any())
    @if (old('name') || old('phone') || old('role'))
        showTab('register');
    @endif
@endif
</script>

</body>
</html>