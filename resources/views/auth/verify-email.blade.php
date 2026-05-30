<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email — RentWheels</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--gray-50);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;">
    <div style="width:100%;max-width:460px;">

        <div style="text-align:center;margin-bottom:32px;">
            <a href="{{ route('home') }}"
               style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.75rem;font-weight:900;color:var(--navy-900);">
                Rent<span style="color:var(--brand-400);">Wheels</span>
            </a>
        </div>

        <div class="card">
            <div class="card-body" style="padding:32px;text-align:center;">
                <div style="font-size:3rem;margin-bottom:16px;">📧</div>
                <h3 style="margin-bottom:12px;">Verifikasi Email Anda</h3>
                <p class="text-muted text-sm" style="line-height:1.8;margin-bottom:24px;">
                    Kami telah mengirimkan link verifikasi ke email Anda.
                    Klik link tersebut untuk mengaktifkan akun dan mulai memesan kendaraan.
                </p>

                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success" style="text-align:left;">
                        ✅ Link verifikasi baru telah dikirim ke email Anda.
                    </div>
                @endif

                <div style="display:flex;flex-direction:column;gap:10px;">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">
                            📨 Kirim Ulang Link Verifikasi
                        </button>
                    </form>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-block"
                                style="color:var(--gray-500);">
                            Keluar dari Akun
                        </button>
                    </form>
                </div>

                <p class="text-xs text-muted" style="margin-top:16px;">
                    Tidak menerima email? Cek folder spam/junk atau coba kirim ulang.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
