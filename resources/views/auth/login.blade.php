<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Masuk — Rental Mobil</title>
    @vite('resources/css/login.css')
</head>
<body>

<div class="auth-wrap">

  {{-- ─── Hero (kiri di desktop, atas di mobile) ─────────── --}}
  <section class="auth-hero">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>

    <a href="{{ route('dashboard') }}" class="auth-logo">Rental<span>Mobil</span></a>

    <div class="auth-hero-body">
      <h1>Sewa mobil mudah,<br>aman & terpercaya.</h1>
      <p>Pesan mobil favorit Anda dalam hitungan menit. Pilih ratusan unit dari mitra resmi di seluruh Indonesia.</p>

      {{-- Stats — hanya tampil di desktop --}}
      <div class="auth-stats">
        <div><b>500+</b><span>Mobil</span></div>
        <div><b>50+</b><span>Kota</span></div>
        <div><b>4.9★</b><span>Rating</span></div>
      </div>
    </div>

    <p class="auth-copy">© {{ date('Y') }} RentalMobil. Semua hak dilindungi.</p>
  </section>

  {{-- ─── Form (kanan di desktop, bawah di mobile) ────────── --}}
  <section class="auth-form-wrap">
    <div class="auth-card">

      {{-- Tabs --}}
      <div class="tab-row">
        <button class="tab-btn active" id="tab-login"    onclick="showTab('login')"    type="button">Masuk</button>
        <button class="tab-btn"        id="tab-register" onclick="showTab('register')" type="button">Daftar</button>
      </div>

      {{-- ════ LOGIN FORM ════ --}}
      <form id="form-login" method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Session Status --}}
        @if (session('status'))
          <div class="toast show success" id="toast">{{ session('status') }}</div>
        @endif

        @if ($errors->any() && !old('name'))
          <div class="toast show error" id="toast">{{ $errors->first() }}</div>
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
            @unless(old('name'))
              <div class="field-error">{{ $message }}</div>
            @endunless
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
            @unless(old('name'))
              <div class="field-error">{{ $message }}</div>
            @endunless
          @enderror

          {{-- Remember & Lupa --}}
          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
            <label class="check">
              <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
              <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="link">Lupa kata sandi?</a>
            @endif
          </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" id="btn-login">
          <div class="spinner" id="spin-login"></div>
          Masuk
        </button>

        {{-- Divider --}}
        <div class="divider"><span>atau</span></div>

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
      </form>

      {{-- ════ REGISTER FORM ════ --}}
      <form id="form-register" method="POST" action="{{ route('register') }}" style="display:none;">
        @csrf

        @if ($errors->any() && old('name'))
          <div class="toast show error">{{ $errors->first() }}</div>
        @endif

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
          <label class="form-label" for="reg-no_hp">
            Nomor HP <span class="req">*</span>
          </label>
          <input
            class="form-input @error('no_hp') error @enderror"
            type="tel"
            id="reg-no_hp"
            name="no_hp"
            placeholder="08xxxxxxxxxx"
            value="{{ old('no_hp') }}"
            autocomplete="tel"
            required
            oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
            onpaste="handlePasteNoHp(event)"
          >
          @error('no_hp')
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
              autocomplete="new-password"
              minlength="8"
              required
            >
            <button type="button" class="input-icon" onclick="togglePw('pw-reg', this)">👁️</button>
          </div>
          @error('password')
            <div class="field-error">{{ $message }}</div>
          @enderror
        </div>

        {{-- Konfirmasi Password --}}
        <div class="form-group">
          <label class="form-label" for="pw-confirm">
            Konfirmasi Kata Sandi <span class="req">*</span>
          </label>
          <div class="input-wrap">
            <input
              class="form-input"
              type="password"
              id="pw-confirm"
              name="password_confirmation"
              placeholder="Ulangi kata sandi"
              autocomplete="new-password"
              required
            >
            <button type="button" class="input-icon" onclick="togglePw('pw-confirm', this)">👁️</button>
          </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-primary" id="btn-register">
          <div class="spinner" id="spin-register"></div>
          Buat Akun
        </button>

        {{-- Divider --}}
        <div class="divider"><span>atau</span></div>

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

    </div>{{-- /.auth-card --}}
  </section>

</div>{{-- /.auth-wrap --}}

{{-- Floating toast untuk JS --}}
<div class="toast" id="toast-float"></div>

<script>
var toastTimer;

/* ─── Tab Switch ──────────────────────────────────────────── */
function showTab(tab) {
    var isLogin = tab === 'login';
    document.getElementById('tab-login').classList.toggle('active', isLogin);
    document.getElementById('tab-register').classList.toggle('active', !isLogin);
    document.getElementById('form-login').style.display    = isLogin ? '' : 'none';
    document.getElementById('form-register').style.display = isLogin ? 'none' : '';
}

/* ─── Toggle Password ─────────────────────────────────────── */
function togglePw(id, btn) {
    var input = document.getElementById(id);
    var isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.textContent = isText ? '👁️' : '🙈';
}

/* ─── Google Auth ─────────────────────────────────────────── */
function handleGoogleAuth() {
    showToast('🔄 Mengarahkan ke Google...');
    setTimeout(function () {
        window.location.href = '{{ url("/auth/google") }}';
    }, 800);
}

/* ─── Loading State ───────────────────────────────────────── */
function setLoading(btnId, spinId, loading) {
    var btn  = document.getElementById(btnId);
    var spin = document.getElementById(spinId);
    if (btn)  btn.disabled = loading;
    if (spin) spin.style.display = loading ? 'block' : 'none';
}

/* ─── Floating Toast ──────────────────────────────────────── */
function showToast(msg) {
    var t = document.getElementById('toast-float');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.classList.remove('show'); }, 3000);
}

/* ─── Paste HP ────────────────────────────────────────────── */
function handlePasteNoHp(event) {
    var pasted = (event.clipboardData || window.clipboardData).getData('text');
    event.preventDefault();
    document.getElementById('reg-no_hp').value = pasted.replace(/[^0-9+]/g, '');
}

/* ─── Submit loading ──────────────────────────────────────── */
document.getElementById('form-login').addEventListener('submit', function () {
    setLoading('btn-login', 'spin-login', true);
});

document.getElementById('form-register').addEventListener('submit', function (e) {
    var pw      = document.getElementById('pw-reg').value;
    var confirm = document.getElementById('pw-confirm').value;
    if (pw !== confirm) {
        e.preventDefault();
        showToast('❌ Konfirmasi kata sandi tidak cocok');
        return;
    }
    setLoading('btn-register', 'spin-register', true);
});

/* ─── Auto buka tab register jika ada error register ─────── */
@if ($errors->any() && old('name'))
    showTab('register');
@endif
</script>

</body>
</html>