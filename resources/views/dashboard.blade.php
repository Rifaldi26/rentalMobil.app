{{-- 
    View ini tidak pernah ditampilkan langsung karena route /dashboard
    langsung redirect ke dashboard role yang sesuai.
    File ini ada hanya untuk mencegah ViewNotFound exception jika ada
    kode lain yang memanggil view('dashboard').
--}}
@php
    $user = auth()->user();
    if ($user?->isAdmin())   { redirect()->route('admin.dashboard')->send();   exit; }
    if ($user?->isPartner()) { redirect()->route('partner.dashboard')->send(); exit; }
    redirect()->route('customer.bookings.index')->send();
@endphp
