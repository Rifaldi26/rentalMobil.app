<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(private readonly ChatService $chatService) {}

    public function show(Booking $booking): View
    {
        $this->authorize('view', $booking);

        $messages = $this->chatService->getMessages($booking, auth()->user());
        $booking->load(['vehicle', 'user']);

        return view('customer.chat.show', compact('booking', 'messages'));
    }

    public function send(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        $request->validate(['content' => ['required', 'string', 'max:1000']]);

        $message = $this->chatService->send($booking, auth()->user(), $request->content);

        return response()->json([
            'message' => [
                'id'         => $message->id,
                'content'    => $message->content,
                'sender_id'  => $message->sender_id,
                'sender'     => ['id' => $message->sender->id, 'name' => $message->sender->name],
                'created_at' => $message->created_at->toISOString(),
            ]
        ]);
    }
    public function newMessages(Request $request, Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        $after = $request->integer('after', 0);

        $messages = $booking->messages()
            ->with('sender:id,name,avatar')
            ->where('id', '>', $after)
            ->where('sender_id', '!=', auth()->id())
            ->orderBy('id')
            ->get()
            ->map(fn($msg) => [
                'id'         => $msg->id,
                'content'    => $msg->content,
                'sender_id'  => $msg->sender_id,
                'created_at' => $msg->created_at->toISOString(),
                'sender'     => ['id' => $msg->sender->id, 'name' => $msg->sender->name],
            ]);

        return response()->json(['messages' => $messages]);
    }
}