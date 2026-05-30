<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(private readonly ChatService $chatService) {}

    public function index(): View
    {
        $bookings = Booking::has('messages')
            ->with(['user', 'vehicle', 'messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->latest()
            ->paginate(20);

        return view('admin.chat.index', compact('bookings'));
    }
    /**
     * Tampilkan halaman chat admin dengan pelanggan untuk booking tertentu.
     */
    public function show(Booking $booking): View
    {
        $messages = $this->chatService->getMessages($booking, auth()->user());
        $booking->load(['vehicle', 'user']);

        return view('admin.chat.show', compact('booking', 'messages'));
    }

    /**
     * Kirim pesan dari admin ke pelanggan.
     */
    public function send(Request $request, Booking $booking): JsonResponse
    {
        $request->validate(['content' => ['required', 'string', 'max:1000', 'min:1']]);

        $message = $this->chatService->send($booking, auth()->user(), $request->content);

        return response()->json([
            'message' => [
                'id'         => $message->id,
                'content'    => $message->content,
                'sender_id'  => $message->sender_id,
                'sender'     => ['id' => $message->sender->id, 'name' => $message->sender->name],
                'created_at' => $message->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Hitung pesan belum dibaca untuk badge notifikasi di navbar.
     */
    public function unreadCount(): JsonResponse
    {
        return response()->json([
            'count' => $this->chatService->unreadCount(auth()->user()),
        ]);
    }
}
