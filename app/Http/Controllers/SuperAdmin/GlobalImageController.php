<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GlobalImageController extends Controller
{
    public function index()
    {
        $images = GlobalProductImage::orderBy('title')->paginate(50);
        $totalCount = GlobalProductImage::count();
        return view('super_admin.global_images.index', compact('images', 'totalCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:global_product_images',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('image')->store('global_product_images', 'public');

        GlobalProductImage::create([
            'title'      => strtolower(trim($request->title)),
            'image_path' => $path,
            'status'     => true
        ]);

        return redirect()->back()->with('success', 'Global product image added successfully.');
    }

    public function destroy($id)
    {
        $image = GlobalProductImage::findOrFail($id);

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Global product image deleted successfully.');
    }

    /**
     * Delete ALL global product images and clear storage directory.
     */
    public function clearAll()
    {
        $images = GlobalProductImage::all();
        $count  = $images->count();

        foreach ($images as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        GlobalProductImage::truncate();

        return redirect()->back()->with('success', "Cleared all {$count} global product images successfully.");
    }

    /**
     * Download a sample CSV template.
     */
    public function downloadSampleCsv()
    {
        $rows = [
            ['product_name', 'image_url'],
            ['Tata Salt', 'https://example.com/tata-salt.jpg'],
            ['Maggi Noodles', 'https://example.com/maggi.jpg'],
            ['Amul Butter', 'https://example.com/amul-butter.jpg'],
        ];

        $csvContent = '';
        foreach ($rows as $row) {
            $csvContent .= implode(',', array_map(fn($v) => '"' . str_replace('"', '""', $v) . '"', $row)) . "\n";
        }

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="global_images_sample.csv"',
        ]);
    }

    /**
     * Bulk import from a CSV: columns product_name, image_url
     */
    public function csvImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $path   = $request->file('csv_file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $nameIdx = array_search('product_name', $header);
        $urlIdx  = array_search('image_url', $header);

        if ($nameIdx === false || $urlIdx === false) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'CSV must have columns: product_name, image_url']);
        }

        if (!Storage::disk('public')->exists('global_product_images')) {
            Storage::disk('public')->makeDirectory('global_product_images');
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $name     = isset($row[$nameIdx]) ? strtolower(trim($row[$nameIdx])) : '';
            $imageUrl = isset($row[$urlIdx])  ? trim($row[$urlIdx]) : '';

            if (empty($name) || empty($imageUrl)) continue;

            if (GlobalProductImage::where('title', $name)->exists()) {
                $skipped++;
                continue;
            }

            try {
                $response = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($imageUrl);

                if ($response->successful() && strlen($response->body()) > 500) {
                    $ext      = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    $filename = Str::slug($name) . '-' . time() . '.' . $ext;
                    $savePath = 'global_product_images/' . $filename;

                    Storage::disk('public')->put($savePath, $response->body());

                    GlobalProductImage::create([
                        'title'      => $name,
                        'image_path' => $savePath,
                        'status'     => true,
                    ]);

                    $imported++;
                } else {
                    $errors++;
                }
            } catch (\Exception $e) {
                $errors++;
            }
        }

        fclose($handle);

        return back()->with('success', "CSV Import done! ✅ Imported: {$imported} | ⏭ Skipped: {$skipped} | ❌ Failed: {$errors}");
    }

    /**
     * Fetch products from Open Food Facts API by category and import images.
     * API docs: https://wiki.openfoodfacts.org/API
     */
    public function fetchFromOpenFoodFacts(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'limit'    => 'required|integer|min:5|max:100',
        ]);

        $category = $request->input('category');
        $limit    = (int) $request->input('limit', 20);

        // Map friendly names to Open Food Facts category tags
        $categoryMap = [
            'fruits'          => 'fruits',
            'vegetables'      => 'vegetables',
            'dairy'           => 'dairies',
            'beverages'       => 'beverages',
            'snacks'          => 'snacks',
            'cereals'         => 'cereals-and-their-products',
            'oils'            => 'vegetable-oils',
            'spices'          => 'spices-and-herbs',
            'sweets'          => 'confectioneries',
            'biscuits'        => 'biscuits-and-cakes',
            'pasta-noodles'   => 'pasta',
            'dry-fruits'      => 'dried-fruits',
            'bread'           => 'breads',
            'sauces'          => 'sauces',
            'soups'           => 'soups',
        ];

        $tag = $categoryMap[$category] ?? $category;

        // Open Food Facts Search API - search by category tag
        $apiUrl = "https://world.openfoodfacts.org/cgi/search.pl";

        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'GroceryApp/1.0 (admin@grocery.app)'])
                ->get($apiUrl, [
                    'action'       => 'process',
                    'tagtype_0'    => 'categories',
                    'tag_contains_0' => 'contains',
                    'tag_0'        => $tag,
                    'json'         => 1,
                    'page_size'    => $limit,
                    'page'         => 1,
                    'fields'       => 'product_name,image_front_small_url,image_front_url',
                    'lc'           => 'en',
                    'cc'           => 'in', // India
                ]);

            if (!$response->successful()) {
                return back()->withErrors(['category' => 'Could not connect to Open Food Facts API. Please try again.']);
            }

            $data     = $response->json();
            $products = $data['products'] ?? [];

            if (!Storage::disk('public')->exists('global_product_images')) {
                Storage::disk('public')->makeDirectory('global_product_images');
            }

            $imported = 0;
            $skipped  = 0;
            $errors   = 0;

            foreach ($products as $product) {
                $productName = trim($product['product_name'] ?? '');
                $imageUrl    = $product['image_front_small_url'] ?? $product['image_front_url'] ?? '';

                if (empty($productName) || empty($imageUrl)) {
                    $skipped++;
                    continue;
                }

                $titleLower = strtolower($productName);

                if (GlobalProductImage::where('title', $titleLower)->exists()) {
                    $skipped++;
                    continue;
                }

                try {
                    $imgResponse = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($imageUrl);

                    if ($imgResponse->successful() && strlen($imgResponse->body()) > 500) {
                        $filename = Str::slug($titleLower) . '-off-' . time() . '.jpg';
                        $savePath = 'global_product_images/' . $filename;

                        Storage::disk('public')->put($savePath, $imgResponse->body());

                        GlobalProductImage::create([
                            'title'      => $titleLower,
                            'image_path' => $savePath,
                            'status'     => true,
                        ]);

                        $imported++;
                    } else {
                        $errors++;
                    }
                } catch (\Exception $e) {
                    $errors++;
                }
            }

            return back()->with('success', "Open Food Facts Import done! ✅ Imported: {$imported} | ⏭ Skipped (duplicates/empty): {$skipped} | ❌ Failed: {$errors}");

        } catch (\Exception $e) {
            return back()->withErrors(['category' => 'API Error: ' . $e->getMessage()]);
        }
    }
}
