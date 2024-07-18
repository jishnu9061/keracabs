<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    /**
     * Create a new message instance.
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Contact Form Submission')
            ->markdown('mail.contact-mail')
            ->with([
                'name' => $this->contact->name,
                'email' => $this->contact->email,
                'phone' => $this->contact->phone,
                'subject' => $this->contact->subject,
                'message' => $this->contact->message,
            ]);
    }
}
