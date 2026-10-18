<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactEnquiry extends Mailable
{
    public function __construct(public string $senderName, public string $senderEmail, public string $enquirySubject, public string $enquiryMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'MetoHub enquiry: '.$this->enquirySubject,
            replyTo: [new Address($this->senderEmail, $this->senderName)]);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.contact-enquiry');
    }
}
