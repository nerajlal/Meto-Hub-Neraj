<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PushOrderToDartPos
{
    /**
     * Create the event listener.
     */
    public function __construct(protected \App\Services\DartPosService $dartPos)
    {
        //
    }

    /**
     * Handle the event.
     * Note: Replace $event type-hint with your actual OrderCreated event class once it exists.
     */
    public function handle(object $event): void
    {
        // Example: $order = $event->order;
        
        // $payload = [
        //     'reference' => $order->order_number,
        //     'customer_phone' => $order->user->phone ?? null,
        //     'total' => $order->total,
        //     'items' => $order->items->map(function($item) {
        //         return [
        //             'sku' => $item->variant->sku ?? null,
        //             'quantity' => $item->quantity,
        //             'price' => $item->price
        //         ];
        //     })->toArray(),
        //     'source' => 'online'
        // ];
        
        // $this->dartPos->pushOrder($payload);
    }
}
