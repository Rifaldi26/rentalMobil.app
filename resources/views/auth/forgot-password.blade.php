<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password — RentWheels</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="background:var(--gray-50);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px;">
    <div style="width:100%;max-width:440px;">

        <div style="text-align:center;margin-bottom:32px;">
            <a href="{{ route('home') }}"
               style="font-family:'Plus Jakarta Sans',sans-serif;font-size:1.75rem;font-weight:900;color:var(--navy-900);">
                Rent<span style="color:var(--brand-400);">Wheels</span>
            </a>
        </div>

        <div class="card">
            <div class="card-body" style="padding:32px;">
                <div style="text-align:center;margin-bottom:20px;">
                    <div style="font-size:2.5rem;margin-bottom:8px;">🔑</div>
                    <h3>Lupa Password?</h3>
                    <p class="text-muted text-sm" style="margin-top:6px;">
                        Masukkan email Anda dan kami akan mengirimkan link reset password.
                    </p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">✅ {{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $e) <div>⚠️ {{ $e }}</div> @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               value="{{ old('email') }}" autofocus required
                               placeholder="email@example.com">
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        📧 Kirim Link Reset
                    </button>
                </form>

                <div style="text-align:center;margin-top:16px;">
                    <a href="{{ route('login') }}" style="font-size:.875rem;color:var(--brand-500);">
                        ← Kembali ke Halaman Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
