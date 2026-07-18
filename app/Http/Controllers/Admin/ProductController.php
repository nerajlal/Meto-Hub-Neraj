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

        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $zohoConnected = \App\Models\TenantZohoToken::where('tenant_id', $tenantId)->exists();

        return view('admin.products.index', compact('products', 'total', 'active', 'draft', 'archived', 'types', 'vendors', 'zohoConnected'));
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

    public function downloadSample()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $headersList = [
            'Title', 'Description', 'Status', 'Product Type', 'Vendor', 'Tags', 
            'Min Order Qty', 'Max Order Qty', 'Continue Selling (Yes/No)', 
            'Variant Size', 'SKU', 'Price', 'Compare Price', 'Stock'
        ];

        // Set Headers
        foreach ($headersList as $index => $header) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
        }
        
        // Sample 1
        $sample1 = [
            'Fresh Organic Apples', 'Delicious crisp apples directly from the farm.', 'active', 'Grocery', 'Local Farms', 'Fruits, Organic, Fresh',
            '1', '10', 'No', 
            '1kg', 'APP-1KG', '199.00', '250.00', '50'
        ];
        foreach ($sample1 as $index => $val) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col . '2', $val);
        }
        
        // Sample 2
        $sample2 = [
            'Premium Almonds', 'High quality roasted almonds.', 'active', 'Dry Fruits', 'Nutty Delights', 'Nuts, Premium',
            '', '', 'Yes', 
            '500g', 'ALM-500G', '450.00', '500.00', '0'
        ];
        foreach ($sample2 as $index => $val) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($index + 1);
            $sheet->setCellValue($col . '3', $val);
        }
        
        $fileName = 'products_import_sample.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        $headers = [
            "Content-type" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        
        $callback = function() use($writer) {
            $writer->save('php://output');
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ]);

        $file = $request->file('import_file');
        $path = $file->getRealPath();
        $extension = $file->getClientOriginalExtension();

        if (in_array(strtolower($extension), ['xlsx', 'xls'])) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        } else {
            $data = array_map('str_getcsv', file($path));
        }
        
        // Clean up data to remove purely empty rows
        $data = array_filter($data, function($row) {
            return count(array_filter($row, fn($cell) => trim((string)$cell) !== '')) > 0;
        });
        $data = array_values($data); // Re-index

        if (count($data) < 2) {
            return back()->withErrors(['import_file' => 'The uploaded file is empty or invalid.']);
        }

        // --- INTELLIGENT HEADER DETECTION ---
        // Tally exports often have 10-15 rows of company metadata before the actual headers.
        // We will scan the first 20 rows to find the headers.
        $headerRowIndex = 0;
        $titleIdx = -1;
        $skuIdx = -1;
        $priceIdx = -1;
        $stockIdx = -1;
        
        $descIdx = -1;
        $statusIdx = -1;
        $typeIdx = -1;
        $vendorIdx = -1;
        $tagsIdx = -1;
        $minQtyIdx = -1;
        $maxQtyIdx = -1;
        $continueIdx = -1;
        $sizeIdx = -1;
        $compareIdx = -1;
        
        $isTallyStockSummary = false;

        $titleKeys = ['title', 'name', 'item name', 'product name', 'particulars'];
        $skuKeys = ['sku', 'part no', 'alias', 'item code'];
        $priceKeys = ['price', 'rate', 'standard price', 'mrp'];
        $stockKeys = ['stock', 'qty', 'closing balance', 'quantity'];
        
        $descKeys = ['description', 'desc'];
        $statusKeys = ['status'];
        $typeKeys = ['product type', 'type'];
        $vendorKeys = ['vendor', 'brand'];
        $tagsKeys = ['tags', 'categories'];
        $minQtyKeys = ['min order qty', 'min qty', 'minimum order'];
        $maxQtyKeys = ['max order qty', 'max qty', 'maximum order'];
        $continueKeys = ['continue selling', 'continue selling (yes/no)', 'open when out of stock'];
        $sizeKeys = ['variant size', 'size', 'weight', 'volume'];
        $compareKeys = ['compare price', 'compare at price', 'mrp'];

        for ($i = 0; $i < min(20, count($data)); $i++) {
            $row = array_map('trim', array_map('strtolower', array_map('strval', $data[$i])));
            
            // Check if this row is the start of a Tally Stock Summary ("Closing Balance" super-header)
            if (in_array('closing balance', $row) && in_array('opening balance', $row)) {
                $isTallyStockSummary = true;
                $closingBalanceIdx = array_search('closing balance', $row);
                $inwardsIdx = array_search('inwards', $row);
                $openingIdx = array_search('opening balance', $row);
                
                // The actual data headers are usually on the NEXT row in Tally
                if (isset($data[$i+1])) {
                    $nextRow = array_map('trim', array_map('strtolower', array_map('strval', $data[$i+1])));
                    // Find "Particulars" (Item Name) which might be on the previous row or this row at index 0
                    $titleIdx = 0; 
                    
                    // The "Quantity", "Rate", "Value" under "Closing Balance"
                    // Tally puts Quantity at $closingBalanceIdx, Rate at $closingBalanceIdx + 1
                    $stockIdx = $closingBalanceIdx; 
                    $priceIdx = $closingBalanceIdx + 1;

                    // Fallback price indices
                    $inwardsPriceIdx = $inwardsIdx !== false ? $inwardsIdx + 1 : -1;
                    $openingPriceIdx = $openingIdx !== false ? $openingIdx + 1 : -1;
                    
                    $headerRowIndex = $i + 1; // Data starts after the sub-header
                    break;
                }
            }

            // Standard flat CSV/Excel header detection
            foreach ($row as $index => $colName) {
                if ($titleIdx === -1 && in_array($colName, $titleKeys)) $titleIdx = $index;
                if ($skuIdx === -1 && in_array($colName, $skuKeys)) $skuIdx = $index;
                if ($priceIdx === -1 && in_array($colName, $priceKeys)) $priceIdx = $index;
                if ($stockIdx === -1 && in_array($colName, $stockKeys)) $stockIdx = $index;
                
                if ($descIdx === -1 && in_array($colName, $descKeys)) $descIdx = $index;
                if ($statusIdx === -1 && in_array($colName, $statusKeys)) $statusIdx = $index;
                if ($typeIdx === -1 && in_array($colName, $typeKeys)) $typeIdx = $index;
                if ($vendorIdx === -1 && in_array($colName, $vendorKeys)) $vendorIdx = $index;
                if ($tagsIdx === -1 && in_array($colName, $tagsKeys)) $tagsIdx = $index;
                if ($minQtyIdx === -1 && in_array($colName, $minQtyKeys)) $minQtyIdx = $index;
                if ($maxQtyIdx === -1 && in_array($colName, $maxQtyKeys)) $maxQtyIdx = $index;
                if ($continueIdx === -1 && in_array($colName, $continueKeys)) $continueIdx = $index;
                if ($sizeIdx === -1 && in_array($colName, $sizeKeys)) $sizeIdx = $index;
                if ($compareIdx === -1 && in_array($colName, $compareKeys)) $compareIdx = $index;
            }

            if ($titleIdx !== -1 && ($priceIdx !== -1 || $stockIdx !== -1)) {
                $headerRowIndex = $i;
                break;
            }
        }

        if ($titleIdx === -1 || $priceIdx === -1) {
            return back()->withErrors(['import_file' => 'Could not find required columns (Name/Particulars, Rate/Price) in your file. Ensure your file has valid headers.']);
        }

        // Remove the header rows from data so we only process the items
        $data = array_slice($data, $headerRowIndex + 1);

        $tenantId = session('active_tenant_id') ?? request()->route('tenant') ?? 1;
        $importedCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if (count($row) <= $titleIdx) continue;
                
                $title = trim((string) $row[$titleIdx]);
                if (empty($title)) continue;

                // Grab raw strings
                $rawPrice = isset($row[$priceIdx]) ? trim((string) $row[$priceIdx]) : '';
                $rawStock = ($stockIdx !== -1 && isset($row[$stockIdx])) ? trim((string) $row[$stockIdx]) : '';
                
                // If it's a Tally export and closing rate is empty, fallback to inwards or opening rate
                if ($isTallyStockSummary && empty($rawPrice)) {
                    if (isset($inwardsPriceIdx) && $inwardsPriceIdx !== -1 && isset($row[$inwardsPriceIdx]) && trim((string)$row[$inwardsPriceIdx]) !== '') {
                        $rawPrice = (string) $row[$inwardsPriceIdx];
                    } elseif (isset($openingPriceIdx) && $openingPriceIdx !== -1 && isset($row[$openingPriceIdx]) && trim((string)$row[$openingPriceIdx]) !== '') {
                        $rawPrice = (string) $row[$openingPriceIdx];
                    }
                }

                $price = (float) preg_replace('/[^0-9.]/', '', $rawPrice);
                $sku = ($skuIdx !== -1 && isset($row[$skuIdx])) ? trim((string) $row[$skuIdx]) : '';
                $stock = (int) preg_replace('/[^0-9.-]/', '', $rawStock);

                // --- NEW FIELDS EXTRACTION ---
                $description = ($descIdx !== -1 && isset($row[$descIdx])) ? trim((string)$row[$descIdx]) : null;
                $status = ($statusIdx !== -1 && isset($row[$statusIdx])) ? trim((string)$row[$statusIdx]) : 'active';
                if (!in_array(strtolower($status), ['active', 'draft'])) $status = 'active';
                
                $type = ($typeIdx !== -1 && isset($row[$typeIdx])) ? trim((string)$row[$typeIdx]) : 'product';
                $vendor = ($vendorIdx !== -1 && isset($row[$vendorIdx])) ? trim((string)$row[$vendorIdx]) : null;
                
                $tags = [];
                if ($tagsIdx !== -1 && isset($row[$tagsIdx])) {
                    $tagsStr = trim((string)$row[$tagsIdx]);
                    if (!empty($tagsStr)) {
                        $tags = array_map('trim', explode(',', $tagsStr));
                    }
                }

                $minQty = null;
                if ($minQtyIdx !== -1 && isset($row[$minQtyIdx])) {
                    $m = trim((string)$row[$minQtyIdx]);
                    if (is_numeric($m) && $m > 0) $minQty = (int)$m;
                }
                
                $maxQty = null;
                if ($maxQtyIdx !== -1 && isset($row[$maxQtyIdx])) {
                    $m = trim((string)$row[$maxQtyIdx]);
                    if (is_numeric($m) && $m > 0) $maxQty = (int)$m;
                }

                $continueSelling = false;
                if ($continueIdx !== -1 && isset($row[$continueIdx])) {
                    $c = strtolower(trim((string)$row[$continueIdx]));
                    if ($c === 'yes' || $c === 'true' || $c === '1') $continueSelling = true;
                }
                
                $variantSize = ($sizeIdx !== -1 && isset($row[$sizeIdx])) ? trim((string)$row[$sizeIdx]) : 'Standard';
                if (empty($variantSize)) $variantSize = 'Standard';
                
                $comparePrice = null;
                if ($compareIdx !== -1 && isset($row[$compareIdx])) {
                    $c = trim((string)$row[$compareIdx]);
                    $parsed = (float) preg_replace('/[^0-9.]/', '', $c);
                    if ($parsed > 0) $comparePrice = $parsed;
                }
                // --- END NEW FIELDS ---

                // Create or update Product
                $product = Product::firstOrCreate([
                    'title' => $title,
                    'tenant_id' => $tenantId,
                ], [
                    'status' => strtolower($status),
                    'type' => $type,
                    'description' => $description,
                    'vendor' => $vendor,
                    'tags' => $tags,
                    'min_order_qty' => $minQty,
                    'max_order_qty' => $maxQty,
                    'continue_selling_when_out_of_stock' => $continueSelling
                ]);

                // Create Variant
                if (empty($sku)) {
                    $sku = \Illuminate\Support\Str::slug($title) . '-' . \Illuminate\Support\Str::slug($variantSize) . '-' . rand(100, 999);
                }

                $product->variants()->updateOrCreate([
                    'sku' => $sku
                ], [
                    'size' => $variantSize,
                    'price' => $price,
                    'compare_at_price' => $comparePrice,
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
