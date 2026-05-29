<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mobil->nama }} — Rental Mobil</title>
    @vite(['resources/css/dashboard.css'])
</head>
<body>

<nav class="nav">
    <button onclick="history.back()"
        style="background:none;border:none;cursor:pointer;padding:8px;display:flex;align-items:center;color:var(--gray-700);">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
    </button>
    <div class="nav-brand" style="font-size:16px;">Detail Mobil</div>
    <div style="width:36px;"></div>
</nav>

<div style="padding:16px 20px 120px;">
    <p style="color:var(--gray-500);text-align:center;padding:40px;">
        Halaman detail mobil sedang dalam pengembangan 🚧
    </p>
</div>

@include('users.partials.bottom-nav')

</body>
</html>