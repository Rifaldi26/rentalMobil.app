<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Message $message) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('booking.' . $this->message->booking_id),
        ];
    }

    /**
     * Nama event yang didengarkan di frontend.
     * Menggunakan dot-prefix agar bisa dioverride tanpa namespace class.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Data yang dikirim ke frontend.
     * Sertakan semua field yang dibutuhkan UI agar tidak perlu request tambahan.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id'         => $this->message->id,
                'booking_id' => $this->message->booking_id,
                'sender_id'  => $this->message->sender_id,
                'content'    => $this->message->content,
                'created_at' => $this->message->created_at->toISOString(),
                'sender'     => [
                    'id'         => $this->message->sender->id,
                    'name'       => $this->message->sender->name,
                    'avatar_url' => $this->message->sender->avatar_url,
                ],
            ],
        ];
    }
}
