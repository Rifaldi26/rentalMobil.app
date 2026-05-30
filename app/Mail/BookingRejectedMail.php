<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Pemesanan {$this->booking->booking_code} Tidak Dapat Diproses",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.rejected',
        );
    }
}