<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class ManualOrderStock
{
    /** Must run inside the order transaction, with the product locked first. */
    public function select(Product $product, ?int $variantId, int $quantity, bool $reserve)
    {
        $query = $product->variants()->orderBy('id')->lockForUpdate();
        $variant = $variantId ? $query->whereKey($variantId)->first() : $query->first();
        if (! $variant) {
            throw ValidationException::withMessages(['items' => 'Choose a valid variant belonging to the selected product.']);
        }
        if ($reserve) {
            if (! $product->continue_selling_when_out_of_stock && $variant->stock < $quantity) {
                throw ValidationException::withMessages(['items' => "There is insufficient stock for {$product->title}."]);
            }
            $variant->decrement('stock', $quantity);
        }

        return $variant;
    }

    /** The caller locks the order first. Only new, explicitly tracked manual items are changed. */
    public function transition(Order $order, string $status): void
    {
        if (($order->status === 'cancelled') === ($status === 'cancelled')) return;
        foreach ($order->items()->orderBy('product_id')->orderBy('id')->lockForUpdate()->get() as $item) {
            $options = $item->options ?? [];
            $stock = $options['manual_stock'] ?? null;
            if (! is_array($stock)) continue; // Never guess stock reservations for historical orders.
            $reserve = $status !== 'cancelled';
            if ((bool) $stock['reserved'] === $reserve) continue;
            $product = Product::where('tenant_id', $order->tenant_id)->lockForUpdate()->find($item->product_id);
            if ($reserve) {
                if (! $product || $product->status !== 'active') {
                    throw ValidationException::withMessages(['status' => 'A product is no longer available. Keep this order cancelled.']);
                }
                $this->select($product, (int) $stock['variant_id'], $item->quantity, true);
            } elseif ($product) {
                // Missing/deleted variants cannot be restocked; never substitute another variant.
                $variant = $product->variants()->whereKey($stock['variant_id'])->lockForUpdate()->first();
                $variant?->increment('stock', $item->quantity);
            }
            $options['manual_stock']['reserved'] = $reserve;
            $item->update(['options' => $options]);
        }
    }
}
