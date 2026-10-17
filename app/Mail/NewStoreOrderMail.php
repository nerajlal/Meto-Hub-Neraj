<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewStoreOrderMail extends Mailable
{
    public function __construct(public Order $order, public string $storeName, public string $orderUrl, public string $currency) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'New order #'.$this->order->order_number.' · '.$this->storeName);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new_store_order');
    }
}
