<!DOCTYPE html>
<html lang="id" x-data="{ show: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password — RentWheels</title>
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
                    <div style="font-size:2.5rem;margin-bottom:8px;">🔒</div>
                    <h3>Buat Password Baru</h3>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $e) <div>⚠️ {{ $e }}</div> @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input"
                               value="{{ old('email', $request->email) }}" required>
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div style="position:relative;">
                            <input :type="show ? 'text' : 'password'" name="password"
                                   class="form-input" required
                                   placeholder="Min. 8 karakter" style="padding-right:44px;">
                            <button type="button" @click="show = !show"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;">
                                <span x-text="show ? '🙈' : '👁'"></span>
                            </button>
                        </div>
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input :type="show ? 'text' : 'password'" name="password_confirmation"
                               class="form-input" required placeholder="Ulangi password baru">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        ✅ Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
