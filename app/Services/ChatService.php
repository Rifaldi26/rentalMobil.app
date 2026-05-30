<?php

namespace App\Services;

use App\Events\MessageSent;
use App\Models\Booking;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ChatService
{
    /**
     * Ambil riwayat pesan untuk sebuah booking.
     *
     * Menggunakan pagination (ambil 100 terakhir) untuk mencegah
     * loading lambat pada booking lama dengan banyak pesan.
     *
     * Juga menandai pesan dari lawan bicara sebagai sudah dibaca.
     */
    public function getMessages(Booking $booking, User $currentUser, int $perPage = 100): Collection
    {
        $messages = $booking->messages()
            ->with('sender:id,name,avatar')
            ->oldest()
            ->limit($perPage)
            ->get();

        // Tandai pesan dari lawan bicara sebagai sudah dibaca
        $booking->messages()
            ->where('sender_id', '!=', $currentUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $messages;
    }

    /**
     * Kirim pesan dan broadcast ke channel private booking.
     */
    public function send(Booking $booking, User $sender, string $content): Message
    {
        // Tentukan receiver: jika sender adalah pelanggan, receiver adalah admin, dan sebaliknya
        $receiver = $sender->isAdmin()
            ? $booking->user
            : User::getOwner();

        $message = $booking->messages()->create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'content'     => trim($content),
        ]);

        $message->load('sender:id,name,avatar');

        broadcast(new MessageSent($message))->toOthers();

        return $message;
    }

    /**
     * Hitung pesan yang belum dibaca untuk user tertentu.
     * Dipakai untuk badge notifikasi di navbar.
     */
    public function unreadCount(User $user): int
    {
        return Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }
}