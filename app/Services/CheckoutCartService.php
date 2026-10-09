<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Product;
use Illuminate\Validation\ValidationException;

class CheckoutCartService
{
    /** Refresh prices and reserve stock while the caller holds a transaction. */
    public function prepare(array $cart): array
    {
        foreach ($cart as &$item) {
            $quantity = filter_var($item['quantity'] ?? null, FILTER_VALIDATE_INT);
            if (!$quantity || $quantity < 1) {
                $this->invalid('Cart quantities must be positive whole numbers.');
            }
            $item['quantity'] = $quantity;
            $item['coupon'] = null;
            unset($item['coupon_code'], $item['effective_price']);

            if (($item['type'] ?? 'product') === 'product') {
                $product = Product::where('status', 'active')->lockForUpdate()->find($item['product_id'] ?? null);
                if (!$product) {
                    $this->invalid('A product in your cart is no longer available.');
                }
                $this->checkLimits($product, $quantity);
                $variant = $this->variant($product, $item['variant_id'] ?? null, $item['size'] ?? null);
                $this->reserve($product, $variant, $quantity);
                $item['price'] = $variant->price;
                $item['original_price'] = $variant->price;
                $item['name'] = $product->title;
                $item['size'] = $variant->size;
                $item['variant_id'] = $variant->id;
                $item['type'] = 'product';
                $item['coupon'] = CartService::getActiveCoupon($product);
            } elseif ($item['type'] === 'bundle') {
                $bundle = Bundle::where('status', 'active')->lockForUpdate()->find($item['bundle_id'] ?? null);
                if (!$bundle || $bundle->products->isEmpty()) {
                    $this->invalid('A bundle in your cart is no longer available.');
                }
                $this->checkLimits($bundle, $quantity);
                foreach ($bundle->products as $product) {
                    if ($product->status !== 'active') {
                        $this->invalid('A bundle product is no longer available.');
                    }
                    $variant = $this->variant($product, $product->pivot->product_variant_id);
                    $this->reserve($product, $variant, $quantity * ($product->pivot->quantity ?? 1));
                }
                $item['price'] = $bundle->total_price;
                $item['original_price'] = $item['price'];
                $item['name'] = $bundle->title;
                $bundle->increment('total_sales', $quantity);
            } else {
                $this->invalid('Invalid cart item.');
            }
        }
        unset($item);

        return $cart;
    }

    private function variant(Product $product, $variantId = null, $size = null)
    {
        $query = $product->variants()->lockForUpdate();
        if ($variantId) {
            $query->whereKey($variantId);
        } elseif ($size) {
            $query->where('size', $size);
        } else {
            $query->orderBy('price')->orderBy('id');
        }
        $variant = $query->first();
        if (!$variant || $variant->price === null) {
            $this->invalid('A selected product variant is no longer available.');
        }

        return $variant;
    }

    private function reserve(Product $product, $variant, int $quantity): void
    {
        if (!$product->continue_selling_when_out_of_stock && $variant->stock < $quantity) {
            $this->invalid("There is insufficient stock for {$product->title}.");
        }
        $variant->decrement('stock', $quantity);
    }

    private function checkLimits($item, int $quantity): void
    {
        if (($item->min_order_qty && $quantity < $item->min_order_qty) ||
            ($item->max_order_qty && $quantity > $item->max_order_qty)) {
            $this->invalid("Please check the purchase quantity for {$item->title}.");
        }
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['cart' => $message]);
    }
}
