<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverMail extends Mailable
{
    use Queueable, SerializesModels;

    public $registrationData;

    /**
     * Create a new message instance.
     *
     * @param array $registrationData
     */
    public function __construct(array $registrationData)
    {
        $this->registrationData = $registrationData;
    }

    /**
     * Get the message envelope.
     */
    public function build(): void
    {
        $this
            ->subject('Driver Registration Confirmation')
            ->markdown('mail.driver-mail')
            ->with('registrationData', $this->registrationData);
    }
}
