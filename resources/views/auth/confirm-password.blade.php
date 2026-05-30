<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Konfirmasi Password — RentWheels</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--gray-50);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;">
    <div style="width:100%;max-width:440px;">

        <div style="text-align:center;margin-bottom:28px;">
            <a href="{{ route('home') }}"
               style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.75rem;font-weight:900;color:var(--navy-900);">
                Rent<span style="color:var(--brand-400);">Wheels</span>
            </a>
        </div>

        <div class="card">
            <div class="card-body" style="padding:32px;">
                <div style="text-align:center;margin-bottom:20px;">
                    <div style="font-size:2.5rem;margin-bottom:8px;">🔐</div>
                    <h3>Konfirmasi Password</h3>
                    <p class="text-muted text-sm" style="margin-top:8px;line-height:1.7;">
                        Untuk keamanan, harap konfirmasi password Anda sebelum melanjutkan ke area ini.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $e) <div>⚠️ {{ $e }}</div> @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-input" required
                               autofocus placeholder="Masukkan password Anda">
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        ✅ Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
