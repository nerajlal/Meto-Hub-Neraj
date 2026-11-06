<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BundleController extends Controller
{
    public function index(Request $request)
    {
        $query = Bundle::with(['products.variants']);

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Type Filter (Tabs)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        } else {
            $query->where('type', 'bundle'); // Default
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

        $bundles = $query->paginate(10);

        if ($request->ajax()) {
            return view('admin.bundles.partials.table', compact('bundles'))->render();
        }

        return view('admin.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $products = Product::with(['variants', 'images'])->where('status', 'active')->get();
        return view('admin.bundles.create', compact('products'));
    }

    public function createPool()
    {
        $products = Product::with(['variants', 'images'])->where('status', 'active')->get();
        return view('admin.bundles.create_pool', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*' => 'required|integer|exists:products,id',
            'variant_ids' => 'nullable|array',
            'variant_ids.*' => 'nullable|integer',
            'quantities' => 'nullable|array',
            'quantities.*' => 'required|integer|min:1|max:10000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'min_order_qty' => 'nullable|integer|min:1',
            'max_order_qty' => 'nullable|integer|min:1' . ($request->filled('min_order_qty') ? '|gte:min_order_qty' : ''),
        ]);

        $items = $this->bundleItems($request);

        $slug = Str::slug($request->title);
        // Ensure unique slug
        $count = Bundle::withoutGlobalScopes()->where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('bundles', 'public');
        }

        DB::transaction(function () use ($request, $slug, $imagePath, $items) {
            $bundle = Bundle::create([
                'title' => $request->title,
                'slug' => $slug,
                'type' => 'bundle',
                'description' => $request->description,
                'image' => $imagePath,
                'status' => $request->status ?? 'draft',
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_qty' => $request->min_order_qty,
                'max_order_qty' => $request->max_order_qty,
            ]);

            foreach ($items as $item) $bundle->products()->attach($item['product_id'], $item['pivot']);
        });

        return redirect()->route('admin.bundles')->with('success', 'Bundle created successfully.');
    }

    public function edit($id)
    {
        $bundle = Bundle::with(['products.variants', 'products.images'])->findOrFail($id);
        $products = Product::with(['variants', 'images'])->where('status', 'active')->get();
        $products = $products->concat($bundle->products)->unique('id')->values();
        
        if ($bundle->type == 'pool') {
            return view('admin.bundles.edit_pool', compact('bundle', 'products'));
        }
        
        return view('admin.bundles.edit', compact('bundle', 'products'));
    }

    public function update(Request $request, $id)
    {
        $bundle = Bundle::findOrFail($id);

        if ($bundle->type == 'pool') {
            $request->validate([
                'title' => 'required|string|max:255',
                'product_ids' => 'required|array|min:1',
                'product_ids.*' => 'exists:products,id',
                'min_quantity' => 'required|integer|min:1',
                'discount_amount' => 'required|numeric|min:0',
            ]);

            $bundle->update([
                'title' => $request->title,
                'min_quantity' => $request->min_quantity,
                'discount_value' => $request->discount_amount,
                'status' => $request->status ?? 'active'
            ]);

            $bundle->products()->sync($request->product_ids);

            return redirect()->route('admin.bundles', ['type' => 'pool'])->with('success', 'Pool updated successfully.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'products' => 'required|array|min:1',
            'products.*' => 'required|integer|exists:products,id',
            'variant_ids' => 'nullable|array',
            'variant_ids.*' => 'nullable|integer',
            'quantities' => 'nullable|array',
            'quantities.*' => 'required|integer|min:1|max:10000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'min_order_qty' => 'nullable|integer|min:1',
            'max_order_qty' => 'nullable|integer|min:1' . ($request->filled('min_order_qty') ? '|gte:min_order_qty' : ''),
        ]);

        $items = $bundle->type === 'pack' ? [] : $this->bundleItems($request);

        $slug = Str::slug($request->title);
        if ($slug !== $bundle->slug) {
            $count = Bundle::withoutGlobalScopes()->where('slug', 'LIKE', "{$slug}%")->where('id', '!=', $id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
        } else {
            $slug = $bundle->slug;
        }

        $data = [
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'status' => $request->status ?? 'draft',
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_qty' => $request->min_order_qty,
            'max_order_qty' => $request->max_order_qty,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('bundles', 'public');
        }

        DB::transaction(function () use ($bundle, $data, $items) {
            $bundle->update($data);
            if ($bundle->type !== 'pack') {
                $bundle->products()->detach();
                foreach ($items as $item) $bundle->products()->attach($item['product_id'], $item['pivot']);
            }
        });

        return redirect()->route('admin.bundles')->with('success', 'Bundle updated successfully.');
    }

    private function bundleItems(Request $request): array
    {
        $items = [];
        foreach (array_values($request->input('products', [])) as $index => $productId) {
            // Product scope uses the authorized store, including superadmin store access.
            $product = Product::with('variants')->where('status', 'active')->find($productId);
            if (!$product) throw ValidationException::withMessages(['products' => 'Choose active products from this store.']);
            $variantId = $request->input("variant_ids.$index");
            $variant = $variantId ? $product->variants->firstWhere('id', $variantId) : ($product->variants->count() === 1 ? $product->variants->first() : null);
            if (!$variant) throw ValidationException::withMessages(["variant_ids.$index" => "Choose a valid variant for {$product->title}."]);
            $items[] = ['product_id' => $product->id, 'pivot' => ['product_variant_id' => $variant->id, 'quantity' => (int) $request->input("quantities.$index", 1)]];
        }
        return $items;
    }

    public function destroy($id)
    {
        $bundle = Bundle::findOrFail($id);
        $bundle->delete();
        return redirect()->route('admin.bundles')->with('success', 'Bundle deleted successfully.');
    }

    public function storePackOf(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:2',
            'pack_price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $variant = ProductVariant::findOrFail($request->variant_id);

        $title = "Pack of {$request->quantity} - {$product->title} - {$variant->size}";
        $slug = Str::slug($title);
        
        // Ensure unique slug
        $count = Bundle::withoutGlobalScopes()->where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        // Calculate discount value to reach the desired pack price
        // Original total = variant price * quantity
        $originalTotal = $variant->price * $request->quantity;
        $discountValue = $originalTotal - $request->pack_price;

        $bundle = Bundle::create([
            'title' => $title,
            'slug' => $slug,
            'type' => 'pack',
            'description' => "Special curated pack of {$request->quantity} units of {$product->title} ({$variant->size}).",
            'status' => 'active',
            'discount_type' => 'fixed',
            'discount_value' => max(0, $discountValue),
        ]);

        // Attach the product with quantity and variant_id
        $bundle->products()->attach($product->id, [
            'quantity' => $request->quantity,
            'product_variant_id' => $variant->id
        ]);

        return redirect()->route('admin.bundles')->with('success', "Pack of {$request->quantity} created successfully.");
    }

    public function storePool(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'discount_amount' => 'required|numeric|min:0',
        ]);

        $title = $request->title;
        $slug = Str::slug($title);
        $count = Bundle::withoutGlobalScopes()->where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) $slug .= '-' . ($count + 1);

        $bundle = Bundle::create([
            'title' => $title,
            'slug' => $slug,
            'type' => 'pool',
            'min_quantity' => $request->min_quantity,
            'description' => "Bundle pool for multiple selected products.",
            'status' => 'active',
            'discount_type' => 'fixed',
            'discount_value' => $request->discount_amount,
        ]);

        $bundle->products()->attach($request->product_ids);

        return redirect()->route('admin.bundles', ['type' => 'pool'])->with('success', 'Pool bundle created successfully!');
    }
}
