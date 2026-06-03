<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Email — RentalMobil</title>
  <!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
  <style>
    /* Reset */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f4f8; color: #1a202c; -webkit-font-smoothing: antialiased; }
    a { text-decoration: none; }
    img { border: 0; display: block; }

    /* Layout */
    .wrapper   { width: 100%; background: #f0f4f8; padding: 40px 16px; }
    .container { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }

    /* Header */
    .header {
      background: linear-gradient(160deg, #0a1628 0%, #1a3a6b 100%);
      padding: 36px 40px 32px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .header::before {
      content: '';
      position: absolute;
      width: 200px; height: 200px;
      border-radius: 50%;
      background: rgba(255,255,255,.04);
      top: -80px; right: -60px;
    }
    .brand {
      font-size: 24px;
      font-weight: 800;
      color: #ffffff;
      letter-spacing: -.5px;
      position: relative;
    }
    .brand span { color: #fb923c; }

    .header-icon {
      margin: 20px auto 0;
      width: 64px; height: 64px;
      background: rgba(255,255,255,.12);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px;
      position: relative;
    }

    /* Body */
    .body { padding: 36px 40px; }

    .greeting {
      font-size: 20px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 12px;
    }

    .text {
      font-size: 15px;
      line-height: 1.7;
      color: #4b5563;
      margin-bottom: 16px;
    }

    /* CTA Button */
    .btn-wrap { text-align: center; margin: 32px 0; }
    .btn {
      display: inline-block;
      background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
      color: #ffffff !important;
      font-size: 15px;
      font-weight: 700;
      padding: 14px 36px;
      border-radius: 10px;
      letter-spacing: .2px;
      box-shadow: 0 4px 14px rgba(37,99,235,.35);
    }

    /* Divider */
    .divider { height: 1px; background: #e5e7eb; margin: 24px 0; }

    /* URL fallback */
    .url-label { font-size: 12px; color: #9ca3af; margin-bottom: 6px; }
    .url-box {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 10px 14px;
      font-size: 11px;
      color: #6b7280;
      word-break: break-all;
      line-height: 1.5;
    }

    /* Expire note */
    .expire-note {
      background: #fff7ed;
      border-left: 3px solid #fb923c;
      border-radius: 0 8px 8px 0;
      padding: 12px 16px;
      font-size: 13px;
      color: #92400e;
      margin: 24px 0 0;
    }
    .expire-note strong { color: #c2410c; }

    /* Footer */
    .footer {
      background: #f9fafb;
      border-top: 1px solid #e5e7eb;
      padding: 24px 40px;
      text-align: center;
    }
    .footer p { font-size: 12px; color: #9ca3af; line-height: 1.7; }
    .footer a { color: #2563eb; font-weight: 600; }

    /* Mobile */
    @media (max-width: 480px) {
      .body, .footer { padding: 28px 24px; }
      .header { padding: 28px 24px 24px; }
    }
  </style>
</head>
<body>
<div class="wrapper">
  <div class="container">

    {{-- ─── Header ─── --}}
    <div class="header">
      <div class="brand">Rental<span>Mobil</span></div>
      <div class="header-icon">✉️</div>
    </div>

    {{-- ─── Body ─── --}}
    <div class="body">

      <p class="greeting">Halo, {{ $userName }}! 👋</p>

      <p class="text">
        Terima kasih sudah mendaftar di <strong>RentalMobil</strong>.
        Satu langkah lagi — verifikasi alamat email kamu agar akun bisa aktif
        dan kamu bisa langsung memesan mobil.
      </p>

      <div class="btn-wrap">
        <a href="{{ $url }}" class="btn">
          ✅ &nbsp;Verifikasi Email Sekarang
        </a>
      </div>

      <div class="expire-note">
        ⏰ <strong>Link berlaku 60 menit.</strong>
        Jika sudah kedaluwarsa, login dan klik "Kirim Ulang Email Verifikasi".
      </div>

      <div class="divider"></div>

      <p class="text" style="font-size:13px; color:#6b7280;">
        Jika tombol di atas tidak bisa diklik, salin dan tempel URL berikut ke browser kamu:
      </p>
      <p class="url-label">URL Verifikasi</p>
      <div class="url-box">{{ $url }}</div>

      <div class="divider"></div>

      <p class="text" style="font-size:13px; color:#9ca3af;">
        Jika kamu tidak merasa mendaftar di RentalMobil, abaikan email ini.
        Tidak ada tindakan yang diperlukan.
      </p>

    </div>

    {{-- ─── Footer ─── --}}
    <div class="footer">
      <p>
        Email ini dikirim secara otomatis oleh sistem <strong>RentalMobil</strong>.<br>
        Mohon jangan membalas email ini.
      </p>
      <p style="margin-top:8px;">
        &copy; {{ date('Y') }} RentalMobil. All rights reserved.
      </p>
    </div>

  </div>
</div>
</body>
</html>