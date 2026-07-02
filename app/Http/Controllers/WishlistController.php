<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Toggle a product in/out of the user's wishlist.
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to add products to your wishlist.',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();
        $tenantId = session('active_tenant_id') ?? 1;

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'tenant_id' => $tenantId
            ]);
            $wishlisted = true;
        }

        $count = Wishlist::where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'wishlisted' => $wishlisted,
            'count' => $count
        ]);
    }

    /**
     * Display the user's wishlist page.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $tenantId = session('active_tenant_id') ?? 1;
        $tenant = \App\Models\Tenant::find($tenantId);
        $theme = strtolower($tenant->theme ?? 'template_1');
        
        // Resolve theme view directory prefix
        $viewPrefix = 'template_1';
        if (in_array($theme, ['template_2', 'v2', 'velvet'])) {
            $viewPrefix = 'template_2';
        }

        $wishlists = Wishlist::where('user_id', Auth::id())
            ->where('tenant_id', $tenantId)
            ->with(['product.images', 'product.variants'])
            ->get();

        return view($viewPrefix . '.wishlist', compact('wishlists'));
    }
}
