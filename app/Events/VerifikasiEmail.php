<?php

namespace App\Events;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifikasiEmail extends VerifyEmail
{
    /**
     * Override toMail agar menggunakan template Blade custom.
     * $notifiable = instance User yang akan dikirimi email.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verifikasi Email Anda — RentalMobil')
            ->view('emails.verifikasi-email', [
                'url'      => $url,
                'userName' => $notifiable->name,
            ]);
    }
}