<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class OrderShippedMail extends Mailable
{
    public function __construct(public Order $order, public string $storeName) {}
    public function envelope(): Envelope { return new Envelope(subject: 'Shipment update · '.$this->storeName.' · #'.$this->order->order_number); }
    public function content(): Content { return new Content(view: 'emails.order_shipped'); }
}
