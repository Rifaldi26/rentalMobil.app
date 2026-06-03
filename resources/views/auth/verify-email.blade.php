<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <title>Verifikasi Email — RentalMobil</title>
  @vite('resources/css/login.css')
  <style>
    /* ─── Verify-email page overrides ─── */
    .verify-card {
      max-width: 440px;
      margin: 0 auto;
      padding: 0 16px 40px;
    }

    .icon-circle {
      width: 72px;
      height: 72px;
      background: #eff6ff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      margin: 0 auto 20px;
    }

    .vc-title {
      font-size: 22px;
      font-weight: 800;
      color: #111827;
      text-align: center;
      margin-bottom: 10px;
    }

    .vc-sub {
      font-size: 14px;
      color: #6b7280;
      text-align: center;
      line-height: 1.65;
      margin-bottom: 28px;
    }

    .vc-sub strong {
      color: #111827;
    }

    .vc-alert {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      border-radius: 10px;
      padding: 12px 16px;
      font-size: 13px;
      color: #166534;
      text-align: center;
      margin-bottom: 20px;
    }

    .vc-btn {
      display: block;
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #fff;
      font-size: 15px;
      font-weight: 700;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      letter-spacing: .2px;
      box-shadow: 0 4px 14px rgba(37,99,235,.3);
      transition: opacity .15s;
    }

    .vc-btn:hover { opacity: .92; }

    .vc-logout {
      display: block;
      text-align: center;
      margin-top: 16px;
      font-size: 13px;
      color: #9ca3af;
      cursor: pointer;
      background: none;
      border: none;
      width: 100%;
      text-decoration: underline;
    }

    .vc-logout:hover { color: #374151; }

    .vc-steps {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 16px 20px;
      margin-bottom: 24px;
    }

    .vc-steps p {
      font-size: 12px;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-bottom: 10px;
    }

    .vc-step {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 8px;
      font-size: 13px;
      color: #4b5563;
    }

    .vc-step:last-child { margin-bottom: 0; }

    .step-num {
      min-width: 20px;
      height: 20px;
      border-radius: 50%;
      background: #2563eb;
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
    }
  </style>
</head>
<body>

{{-- ─── Hero ─── --}}
<div class="auth-hero">
  <div class="auth-brand">Rental<span>Mobil</span></div>
  <div class="auth-tagline">Sewa mobil mudah, aman & terpercaya</div>
</div>

{{-- ─── Card ─── --}}
<div class="verify-card">
  <div style="background:#fff; border-radius:16px; padding:32px 28px; box-shadow:0 4px 20px rgba(0,0,0,.08); margin-top:-24px; position:relative;">

    {{-- Icon --}}
    <div class="icon-circle">✉️</div>

    {{-- Title --}}
    <h1 class="vc-title">Cek Email Kamu</h1>
    <p class="vc-sub">
      Kami sudah mengirim link verifikasi ke<br>
      <strong>{{ auth()->user()->email }}</strong>
    </p>

    {{-- Success alert --}}
    @if (session('status') == 'verification-link-sent')
      <div class="vc-alert">
        ✅ Link verifikasi baru sudah dikirim! Cek kotak masuk atau folder spam.
      </div>
    @endif

    {{-- Steps --}}
    <div class="vc-steps">
      <p>Cara verifikasi</p>
      <div class="vc-step">
        <div class="step-num">1</div>
        <span>Buka aplikasi email atau Gmail/Outlook kamu</span>
      </div>
      <div class="vc-step">
        <div class="step-num">2</div>
        <span>Cari email dari <strong>RentalMobil</strong> — cek juga folder <em>Spam</em></span>
      </div>
      <div class="vc-step">
        <div class="step-num">3</div>
        <span>Klik tombol <strong>"Verifikasi Email Sekarang"</strong> di dalam email</span>
      </div>
    </div>

    {{-- Resend button --}}
    <form method="POST" action="{{ route('verification.send') }}">
      @csrf
      <button type="submit" class="vc-btn">
        🔄 &nbsp;Kirim Ulang Email Verifikasi
      </button>
    </form>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="vc-logout">
        Gunakan akun lain / Keluar
      </button>
    </form>

  </div>
</div>

</body>
</html>