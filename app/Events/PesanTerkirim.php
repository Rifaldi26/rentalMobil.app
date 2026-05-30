<?php

namespace App\Events;

use App\Models\Pesan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PesanTerkirim implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Pesan $pesan) {}

    /**
     * Channel private per pasangan user: chat.{minId}-{maxId}
     * Kedua sisi (pengirim & penerima) subscribe ke channel yang sama.
     */
    public function broadcastOn(): array
    {
        $ids = [$this->pesan->pengirim_id, $this->pesan->penerima_id];
        sort($ids);
        $channel = 'chat.' . $ids[0] . '-' . $ids[1];

        return [new PrivateChannel($channel)];
    }

    public function broadcastAs(): string
    {
        return 'pesan.baru';
    }

    public function broadcastWith(): array
    {
        return [
            'id'          => $this->pesan->id,
            'pengirim_id' => $this->pesan->pengirim_id,
            'penerima_id' => $this->pesan->penerima_id,
            'isi'         => $this->pesan->isi,
            'waktu'       => $this->pesan->created_at->format('H.i'),
        ];
    }
}
