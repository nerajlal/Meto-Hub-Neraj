<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Collection;
use App\Models\Attribute;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['variants', 'images', 'bundles']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type Filter
        if ($request->filled('type')) {
             $query->where('type', $request->type);
        }

        // Vendor Filter
        if ($request->filled('vendor')) {
             $query->where('vendor', $request->vendor);
        }

        // Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->oldest();
                    break;
                case 'title_asc':
                    $query->orderBy('title', 'asc');
                    break;
                case 'title_desc':
                    $query->orderBy('title', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        $products = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.products.partials.table', compact('products'))->render();
        }
        
        // Stats
        $total = Product::count();
        $active = Product::where('status', 'active')->count();
        $draft = Product::where('status', 'draft')->count();
        $archived = Product::where('status', 'archived')->count();

        // Unique Types and Vendors for Filters
        $types = Product::distinct()->whereNotNull('type')->pluck('type');
        $vendors = Product::distinct()->whereNotNull('vendor')->pluck('vendor');

        return view('admin.products.index', compact('products', 'total', 'active', 'draft', 'archived', 'types', 'vendors'));
    }

    public function create()
    {
        $collections = Collection::all();
        $families = Attribute::where('type', 'family')->get();
        $notes = Attribute::where('type', 'note')->get();
        $packDeals = collect();
        $allTags = Product::whereNotNull('tags')->pluck('tags')->flatten()->unique()->values()->toArray();
        return view('admin.products.create', compact('collections', 'families', 'notes', 'packDeals', 'allTags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:active,draft',
            'min_order_qty' => 'nullable|integer|min:1',
            'max_order_qty' => 'nullable|integer|min:1|gte:min_order_qty',
            'variants' => 'array',
            'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $product = Product::create($request->only([
            'title', 'description', 'status', 'type', 'vendor', 
            'collection_id', 'gender', 'olfactory_family', 
            'intensity', 'oil_concentration', 'notes_top', 'notes_heart', 'notes_base',
            'min_order_qty', 'max_order_qty', 'continue_selling_when_out_of_stock'
        ]));

        if ($request->has('tags_json')) {
            $tagsData = json_decode($request->tags_json, true);
            if (is_array($tagsData)) {
                $product->tags = array_column($tagsData, 'value');
                $product->save();
            }
        }

        // Handle Variants
        if ($request->has('variants')) {
            foreach ($request->variants as $vData) {
                if (!empty($vData['size'])) {
                    $product->variants()->create([
                        'size' => $vData['size'],
                        'stock' => $vData['stock'] ?? 0,
                        'price' => $vData['price'],
                        'compare_at_price' => $vData['compare_at_price'] ?? null,
                        'sku' => Str::upper(Str::slug($product->title)) . '-' . Str::slug($vData['size']) . '-' . rand(100, 999),
                    ]);
                }
            }
        }

        // Sync/Create Variants
        $variantMap = [];
        foreach ($product->variants as $variant) {
            $variantMap[$variant->size] = $variant->id;
        }

        // Handle Pack Deals
        if ($request->has('packs')) {
            foreach ($request->packs as $pData) {
                if (!empty($pData['quantity']) && !empty($pData['pack_price'])) {
                    $variantId = null;
                    // Resolve variant ID from size string
                    $variantSize = $pData['variant_size'] ?? null;
                    if ($variantSize) {
                        $variantId = $variantMap[$variantSize] ?? null;
                    }
                    
                    if (!$variantId) continue;
                    
                    $variant = ProductVariant::find($variantId);
                    if (!$variant) continue;
                    
                    $originalTotal = $variant->price * $pData['quantity'];
                    $discountValue = max(0, $originalTotal - $pData['pack_price']);
                    
                    $title = "Pack of {$pData['quantity']} - {$product->title} - {$variant->size}";
                    
                    $bundle = Bundle::create([
                        'tenant_id' => $product->tenant_id,
                        'title' => $title,
                        'slug' => Str::slug($title) . '-' . $product->id . '-' . rand(100, 999),
                        'type' => 'pack',
                        'discount_type' => 'fixed',
                        'discount_value' => $discountValue,
                        'status' => 'active',
                    ]);
                    
                    $bundle->products()->attach($product->id, [
                        'quantity' => $pData['quantity'],
                        'product_variant_id' => $variantId
                    ]);
                }
            }
        }

        // Handle Media Uploads
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'type' => 'image', // simplified for now
                    'order' => 0
                ]);
            }
        }
        
        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }

    public function edit($id)
    {
        $product = Product::with(['variants', 'images' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        $packDeals = $product->bundles()->where('type', 'pack')->get();
        $collections = Collection::all();
        $families = Attribute::where('type', 'family')->get();
        $notes = Attribute::where('type', 'note')->get();
        $allTags = Product::whereNotNull('tags')->pluck('tags')->flatten()->unique()->values()->toArray();
        return view('admin.products.edit', compact('product', 'collections', 'families', 'notes', 'packDeals', 'allTags'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
             'media.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
             'min_order_qty' => 'nullable|integer|min:1',
             'max_order_qty' => 'nullable|integer|min:1|gte:min_order_qty',
        ]);

        $product = Product::findOrFail($id);
        
        // Max 5 Images Validation
        $currentCount = $product->images()->count();
        $deletedCount = $request->has('deleted_images') ? count($request->deleted_images) : 0;
        $newCount = $request->hasFile('media') ? count($request->file('media')) : 0;
        
        if (($currentCount - $deletedCount + $newCount) > 5) {
            return back()->withInput()->withErrors(['media' => "You can only have a maximum of 5 images. You currently have $currentCount, are deleting $deletedCount, and trying to add $newCount."]);
        }
        
        $product->update($request->only([
            'title', 'description', 'status', 'type', 'vendor', 
            'collection_id', 'gender', 'olfactory_family', 
            'intensity', 'oil_concentration', 'notes_top', 'notes_heart', 'notes_base',
            'min_order_qty', 'max_order_qty', 'continue_selling_when_out_of_stock'
        ]));

        if ($request->has('tags_json')) {
            $tagsData = json_decode($request->tags_json, true);
            if (is_array($tagsData)) {
                $product->tags = array_column($tagsData, 'value');
                $product->save();
            }
        } else {
            // If empty, it won't send tags_json usually or sends empty array
            if ($request->exists('tags_json') && empty($request->tags_json)) {
                $product->tags = [];
                $product->save();
            }
        }

        // Sync Variants
        if ($request->has('variants')) {
            $currentVariantIds = [];
            foreach ($request->variants as $vData) {
                if (!empty($vData['size'])) {
                    $variant = null;
                    if (!empty($vData['id'])) {
                        $variant = $product->variants()->find($vData['id']);
                    }
                    
                    if ($variant) {
                        $variant->update([
                            'size' => $vData['size'],
                            'stock' => $vData['stock'] ?? 0,
                            'price' => $vData['price'],
                            'compare_at_price' => $vData['compare_at_price'] ?? null,
                        ]);
                    } else {
                        $variant = $product->variants()->create([
                            'size' => $vData['size'],
                            'stock' => $vData['stock'] ?? 0,
                            'price' => $vData['price'],
                            'compare_at_price' => $vData['compare_at_price'] ?? null,
                            'sku' => Str::upper(Str::slug($product->title)) . '-' . Str::slug($vData['size']) . '-' . rand(100, 999),
                        ]);
                    }
                    $currentVariantIds[] = $variant->id;
                }
            }
            // Remove variants that were removed in the UI
            $product->variants()->whereNotIn('id', $currentVariantIds)->delete();
        } else {
            $product->variants()->delete();
        }

        // Sync/Create Variants
        $variantMap = [];
        foreach ($product->variants as $variant) {
            $variantMap[$variant->size] = $variant->id;
        }

        // Handle Pack Deals
        if ($request->has('packs')) {
            $currentPackIds = [];
            foreach ($request->packs as $pData) {
                if (!empty($pData['quantity']) && !empty($pData['pack_price'])) {
                    $variantId = $pData['variant_id'] ?? null;
                    
                    // Resolve variant ID from size string if passed from creation UI
                    $variantSize = $pData['variant_size'] ?? null;
                    if (empty($variantId) && $variantSize) {
                        $variantId = $variantMap[$variantSize] ?? null;
                    }
                    
                    if (!$variantId) continue;
                    
                    $variant = ProductVariant::find($variantId);
                    if (!$variant) continue;
                    
                    $originalTotal = $variant->price * $pData['quantity'];
                    $discountValue = max(0, $originalTotal - $pData['pack_price']);
                    
                    $title = "Pack of {$pData['quantity']} - {$product->title} - {$variant->size}";
                    
                    $bundle = null;
                    if (!empty($pData['id'])) {
                        $bundle = Bundle::withoutGlobalScopes()->find($pData['id']);
                    }
                    
                    if ($bundle) {
                        $bundle->update([
                            'title' => $title,
                            'slug' => Str::slug($title) . '-' . $product->id . '-' . rand(100, 999),
                            'discount_value' => $discountValue,
                        ]);
                        // Update pivot
                        $bundle->products()->updateExistingPivot($product->id, [
                            'quantity' => $pData['quantity'],
                            'product_variant_id' => $variantId
                        ]);
                    } else {
                        $bundle = Bundle::create([
                            'tenant_id' => $product->tenant_id,
                            'title' => $title,
                            'slug' => Str::slug($title) . '-' . $product->id . '-' . rand(100, 999),
                            'type' => 'pack',
                            'discount_type' => 'fixed',
                            'discount_value' => $discountValue,
                            'status' => 'active',
                        ]);
                        $bundle->products()->attach($product->id, [
                            'quantity' => $pData['quantity'],
                            'product_variant_id' => $variantId
                        ]);
                    }
                    $currentPackIds[] = $bundle->id;
                }
            }
            
            // Delete packs that were removed
            $allPacks = $product->bundles()->where('type', 'pack')->get();
            foreach ($allPacks as $p) {
                if (!in_array($p->id, $currentPackIds)) {
                    $p->products()->detach();
                    $p->delete();
                }
            }
        } else {
            $allPacks = $product->bundles()->where('type', 'pack')->get();
            foreach ($allPacks as $p) {
                $p->products()->detach();
                $p->delete();
            }
        }

        // Handle Image Deletion
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = $product->images()->find($imageId);
                if ($image) {
                    if (Storage::disk('public')->exists($image->path)) {
                        Storage::disk('public')->delete($image->path);
                    }
                    $image->delete();
                }
            }
        }

        // Handle Scan/Reorder Existing Images
        if ($request->has('media_order')) {
            foreach ($request->media_order as $index => $imageId) {
                $product->images()->where('id', $imageId)->update(['order' => $index]);
            }
        }

        // Handle Media Uploads
        if ($request->hasFile('media')) {
            $startOrder = $product->images()->max('order') + 1; // Start after existing
            foreach ($request->file('media') as $index => $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'type' => 'image',
                    'order' => $startOrder + $index
                ]);
            }
        }

        return redirect()->route('admin.products')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        // Delete images from storage
        foreach($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Product deleted successfully.');
    }

    public function getVariants($id)
    {
        $product = Product::with('variants')->findOrFail($id);
        return response()->json($product->variants);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('import_file');
        $path = $file->getRealPath();

        $data = array_map('str_getcsv', file($path));
        if (count($data) < 2) {
            return back()->withErrors(['import_file' => 'The uploaded file is empty or invalid.']);
        }

        $header = array_map('trim', array_map('strtolower', array_shift($data)));

        // Tally-to-System Mapping
        $titleKeys = ['title', 'name', 'item name', 'product name'];
        $skuKeys = ['sku', 'part no', 'alias', 'item code'];
        $priceKeys = ['price', 'rate', 'standard price', 'mrp'];
        $stockKeys = ['stock', 'qty', 'closing balance', 'quantity'];

        $titleIdx = -1;
        $skuIdx = -1;
        $priceIdx = -1;
        $stockIdx = -1;

        foreach ($header as $index => $colName) {
            if ($titleIdx === -1 && in_array($colName, $titleKeys)) $titleIdx = $index;
            if ($skuIdx === -1 && in_array($colName, $skuKeys)) $skuIdx = $index;
            if ($priceIdx === -1 && in_array($colName, $priceKeys)) $priceIdx = $index;
            if ($stockIdx === -1 && in_array($colName, $stockKeys)) $stockIdx = $index;
        }

        if ($titleIdx === -1 || $priceIdx === -1) {
            return back()->withErrors(['import_file' => 'Could not find required columns (Name, Price) in your CSV. Ensure your file has valid headers.']);
        }

        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $importedCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if (count($row) <= $titleIdx) continue;
                
                $title = trim($row[$titleIdx]);
                if (empty($title)) continue;

                $price = isset($row[$priceIdx]) ? (float) preg_replace('/[^0-9.]/', '', $row[$priceIdx]) : 0;
                $sku = ($skuIdx !== -1 && isset($row[$skuIdx])) ? trim($row[$skuIdx]) : '';
                $stock = ($stockIdx !== -1 && isset($row[$stockIdx])) ? (int) preg_replace('/[^0-9.-]/', '', $row[$stockIdx]) : 0;

                // Create or update Product
                $product = Product::firstOrCreate([
                    'title' => $title,
                    'tenant_id' => $tenantId,
                ], [
                    'status' => 'active',
                    'type' => 'product'
                ]);

                // Create Variant
                if (empty($sku)) {
                    $sku = \Illuminate\Support\Str::slug($title) . '-' . rand(100, 999);
                }

                $product->variants()->updateOrCreate([
                    'sku' => $sku
                ], [
                    'size' => 'Standard',
                    'price' => $price,
                    'stock' => $stock
                ]);

                $importedCount++;
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors(['import_file' => 'Error during import: ' . $e->getMessage()]);
        }

        return redirect()->back()->with('success', "Successfully imported {$importedCount} products.");
    }
}
