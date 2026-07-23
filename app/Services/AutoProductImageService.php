<?php

namespace App\Services;

use App\Models\GlobalProductImage;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AutoProductImageService
{
    /**
     * Try to assign an image to a product.
     * Strategy:
     *   1. Check Global Dictionary (fuzzy match)
     *   2. If no match → search Open Food Facts API
     *   3. If found in API → save to dictionary + assign to product
     *   4. If still nothing → skip (no image is better than a wrong one)
     *
     * @param \App\Models\Product $product
     * @param string              $title
     * @return bool
     */
    public function assignImage($product, string $title): bool
    {
        if ($product->images()->count() > 0) {
            return false; // already has an image, skip
        }

        $searchTerm = strtolower(trim($title));

        // --- Step 1: Check global dictionary (fuzzy) ---
        $globalImage = GlobalProductImage::where('status', true)
            ->whereRaw(
                '(? LIKE CONCAT("%", title, "%") OR title LIKE CONCAT("%", ?, "%"))',
                [$searchTerm, $searchTerm]
            )
            ->orderByRaw('LENGTH(title) DESC')
            ->first();

        if ($globalImage) {
            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $globalImage->image_path,
                'type'       => 'image',
                'order'      => 0,
            ]);
            return true;
        }

        // --- Step 2: Try Open Food Facts API ---
        $apiImagePath = $this->fetchFromOpenFoodFacts($title);

        if ($apiImagePath) {
            // Save to dictionary so future products benefit too
            GlobalProductImage::firstOrCreate(
                ['title' => $searchTerm],
                ['image_path' => $apiImagePath, 'status' => true]
            );

            // Assign to product
            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $apiImagePath,
                'type'       => 'image',
                'order'      => 0,
            ]);
            return true;
        }

        return false;
    }

    /**
     * Search Open Food Facts for a product by name and download its image.
     * Returns the local storage path on success, or null on failure.
     * STRICT matching: we only accept results where the product name closely
     * resembles the search term to avoid wrong images (e.g., "Lemon soda" for "Lemon").
     */
    private function fetchFromOpenFoodFacts(string $productName): ?string
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'GroceryApp/1.0'])
                ->get('https://world.openfoodfacts.org/cgi/search.pl', [
                    'search_terms'  => $productName,
                    'search_simple' => 1,
                    'action'        => 'process',
                    'json'          => 1,
                    'page_size'     => 10,
                    'fields'        => 'product_name,image_front_small_url',
                ]);

            if (!$response->successful()) return null;

            $products    = $response->json('products', []);
            $searchLower = strtolower(trim($productName));
            $searchWords = explode(' ', $searchLower);

            foreach ($products as $p) {
                $apiName  = strtolower(trim($p['product_name'] ?? ''));
                $imageUrl = $p['image_front_small_url'] ?? '';

                if (empty($apiName) || empty($imageUrl)) continue;

                // STRICT rule 1: The API product name must START WITH our search term
                // e.g. "lemon 1kg", "lemon fresh" is OK. "lemon soda", "lemon juice" is NOT.
                if (!str_starts_with($apiName, $searchLower)) {
                    continue;
                }

                // STRICT rule 2: The API name should only add simple suffixes (weight, qty)
                // If the API name has extra WORDS that aren't numbers/units, reject it.
                $apiWords   = explode(' ', $apiName);
                $extraWords = array_slice($apiWords, count($searchWords));
                $allowedSuffixes = ['kg', 'g', 'gm', 'gms', 'ml', 'l', 'ltr', 'litre',
                                    'pack', 'packet', 'piece', 'pcs', 'nos', 'no', 'unit',
                                    '100', '200', '250', '500', '1', '2', '5', '10', 'fresh',
                                    'organic', 'raw', 'natural', 'farm', 'pure'];
                $hasInvalidExtra = false;
                foreach ($extraWords as $word) {
                    if (!is_numeric($word) && !in_array($word, $allowedSuffixes)) {
                        $hasInvalidExtra = true;
                        break;
                    }
                }
                if ($hasInvalidExtra) continue;

                // Download image
                $imgResponse = Http::timeout(8)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get($imageUrl);

                if ($imgResponse->successful() && strlen($imgResponse->body()) > 1000) {
                    if (!Storage::disk('public')->exists('global_product_images')) {
                        Storage::disk('public')->makeDirectory('global_product_images');
                    }

                    $filename = Str::slug(strtolower($productName)) . '-auto-' . time() . '.jpg';
                    $path     = 'global_product_images/' . $filename;

                    Storage::disk('public')->put($path, $imgResponse->body());
                    return $path;
                }
            }
        } catch (\Exception $e) {
            // Silently fail — a missing image is better than a wrong one
        }

        return null;
    }
}
