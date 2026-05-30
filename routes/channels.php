<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Broadcast;

/**
 * Private channel untuk chat booking.
 *
 * Diizinkan masuk jika:
 * - User adalah Admin (pemilik usaha), atau
 * - User adalah pelanggan pemilik booking tersebut
 *
 * Setelah refactor: tidak ada lagi cek partner->vehicles karena
 * semua kendaraan milik satu usaha (Admin).
 */
Broadcast::channel('booking.{bookingId}', function ($user, int $bookingId) {
    if (! $user) return false;

    // Admin bisa akses semua channel booking
    if ($user->isAdmin()) return true;

    // Pelanggan hanya bisa akses booking miliknya sendiri
    $booking = Booking::find($bookingId);
    return $booking && $booking->user_id === $user->id;
});

/**
 * Private channel notifikasi personal per user.
 * Dipakai untuk push notifikasi status booking & pembayaran.
 */
Broadcast::channel('user.{userId}', function ($user, int $userId) {
    return (int) $user->id === $userId;
});
