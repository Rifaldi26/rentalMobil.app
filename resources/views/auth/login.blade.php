<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — RentWheels</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--gray-50);min-height:100vh;display:flex;">

<div style="display:grid;grid-template-columns:1fr 1fr;min-height:100vh;width:100%;">

    {{-- Left Panel (Illustration) --}}
    <div style="background:linear-gradient(135deg,var(--navy-900) 0%,var(--brand-900) 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;position:relative;overflow:hidden;"
         class="auth-panel-left">
        {{-- Decorative circles --}}
        <div style="position:absolute;top:-100px;right:-100px;width:400px;height:400px;border-radius:50%;border:1px solid rgba(255,255,255,.05);"></div>
        <div style="position:absolute;bottom:-80px;left:-80px;width:300px;height:300px;border-radius:50%;border:1px solid rgba(255,255,255,.05);"></div>

        <div style="position:relative;z-index:1;text-align:center;max-width:380px;">
            <div style="font-family:'Sora',sans-serif;font-size:2.5rem;font-weight:800;color:#fff;letter-spacing:-0.04em;margin-bottom:8px;">
                Rent<span style="color:var(--brand-400);">Wheels</span>
            </div>
            <p style="color:rgba(255,255,255,.5);font-size:.9rem;margin-bottom:48px;">
                Platform Sewa Kendaraan Terpercaya
            </p>

            {{-- Feature list --}}
            <div style="display:flex;flex-direction:column;gap:20px;text-align:left;">
                @foreach([
                    ['icon'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>','text'=>'Armada terverifikasi & terawat secara berkala'],
                    ['icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>','text'=>'Pembayaran aman & harga transparan'],
                    ['icon'=>'<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>','text'=>'Dukungan pelanggan 24 jam, 7 hari seminggu'],
                ] as $f)
                <div style="display:flex;align-items:center;gap:14px;">
                    <div style="width:38px;height:38px;background:rgba(20,184,166,.2);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand-400)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            {!! $f['icon'] !!}
                        </svg>
                    </div>
                    <span style="color:rgba(255,255,255,.7);font-size:.875rem;line-height:1.5;">{{ $f['text'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right Panel (Form) --}}
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 40px;background:#fff;">
        <div style="width:100%;max-width:400px;">

            {{-- Mobile Logo --}}
            <div style="display:none;text-align:center;margin-bottom:28px;" class="mobile-logo">
                <div style="font-family:'Sora',sans-serif;font-size:1.75rem;font-weight:800;color:var(--gray-900);">
                    Rent<span style="color:var(--brand-600);">Wheels</span>
                </div>
            </div>

            <h2 style="margin-bottom:8px;">Selamat Datang</h2>
            <p style="color:var(--gray-500);font-size:.9rem;margin-bottom:32px;">
                Masuk untuk melanjutkan perjalanan Anda
            </p>

            {{-- Validation Errors --}}
            @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom:20px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
            @endif

            @if (session('status'))
            <div class="alert alert-success" style="margin-bottom:20px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-icon">
                        <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                               placeholder="nama@email.com"
                               value="{{ old('email') }}"
                               autofocus required
                               style="padding-left:40px;">
                    </div>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                        <label class="form-label" style="margin-bottom:0;">Kata Sandi</label>
                        @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size:.8rem;color:var(--brand-600);font-weight:600;">Lupa sandi?</a>
                        @endif
                    </div>
                    <div class="input-icon" x-data="{ show: false }">
                        <svg class="icon-left" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input :type="show ? 'text' : 'password'" name="password" class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                               placeholder="Kata sandi Anda" required
                               style="padding-left:40px;padding-right:42px;">
                        <button type="button" @click="show = !show"
                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--gray-400);padding:2px;">
                            <svg x-show="!show" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" x-cloak width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;">
                    <input type="checkbox" id="remember" name="remember" style="width:16px;height:16px;accent-color:var(--brand-600);cursor:pointer;">
                    <label for="remember" style="font-size:.875rem;color:var(--gray-700);cursor:pointer;">Ingat saya selama 30 hari</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Masuk
                </button>
            </form>
            
            <div style="margin-top:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <div style="flex:1;height:1px;background:var(--gray-200);"></div>
                    <span style="font-size:.8rem;color:var(--gray-400);">atau</span>
                    <div style="flex:1;height:1px;background:var(--gray-200);"></div>
                </div>
                <a href="{{ route('auth.google') }}"
                   style="display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:11px;border:1px solid var(--gray-200);border-radius:var(--radius-md);font-size:.9rem;font-weight:600;color:var(--gray-700);text-decoration:none;transition:background .15s;"
                   onmouseover="this.style.background='var(--gray-50)'"
                   onmouseout="this.style.background='#fff'">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Masuk dengan Google
                </a>
            </div>

            <div style="text-align:center;margin-top:24px;padding-top:24px;border-top:1px solid var(--gray-100);">
                <span style="font-size:.875rem;color:var(--gray-500);">Belum punya akun?</span>
            <div style="text-align:center;margin-top:24px;padding-top:24px;border-top:1px solid var(--gray-100);">
                <span style="font-size:.875rem;color:var(--gray-500);">Belum punya akun?</span>
                <a href="{{ route('register') }}" style="font-size:.875rem;font-weight:700;color:var(--brand-600);margin-left:5px;">
                    Daftar Sekarang
                </a>
            </div>

            <div style="margin-top:24px;text-align:center;">
                <a href="{{ route('home') }}" style="font-size:.8rem;color:var(--gray-400);display:inline-flex;align-items:center;gap:5px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    body > div { grid-template-columns: 1fr !important; }
    .auth-panel-left { display: none !important; }
    .mobile-logo { display: block !important; }
    body > div > div:last-child { padding: 36px 24px; }
}
</style>

</body>
</html>
