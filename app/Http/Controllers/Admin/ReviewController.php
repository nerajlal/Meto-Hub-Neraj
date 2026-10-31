<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = session('active_tenant_id') ?? request()->attributes->get('resolved_tenant_id');

        $query = Review::with('product')->where('tenant_id', $tenantId)->latest();

        $status = $request->query('status');
        if (in_array($status, ['published', 'pending', 'hidden'], true)) {
            $query->where('status', $status);
        }

        $reviews = $query->paginate(20)->withQueryString();
        $counts = [
            'all' => Review::where('tenant_id', $tenantId)->count(),
            'pending' => Review::where('tenant_id', $tenantId)->where('status', 'pending')->count(),
            'published' => Review::where('tenant_id', $tenantId)->where('status', 'published')->count(),
            'hidden' => Review::where('tenant_id', $tenantId)->where('status', 'hidden')->count(),
        ];

        $products = Product::where('tenant_id', $tenantId)->orderBy('title')->get(['id', 'title']);

        return view('admin.reviews.index', compact('reviews', 'counts', 'status', 'products'));
    }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id') ?? request()->attributes->get('resolved_tenant_id');

        $validated = $request->validate([
            'name' => 'required|string|max:190',
            'review' => 'required|string|max:3000',
            'rating' => 'nullable|integer|min:1|max:5',
            'product_id' => 'nullable|integer',
            'status' => 'nullable|in:published,pending,hidden',
        ]);

        Review::create([
            'tenant_id' => $tenantId,
            'name' => $validated['name'],
            'review' => $validated['review'],
            'rating' => $validated['rating'] ?? 5,
            'product_id' => !empty($validated['product_id']) ? $validated['product_id'] : null,
            'status' => $validated['status'] ?? 'published',
        ]);

        return redirect()->route('admin.reviews')->with('success', 'Review added successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $tenantId = session('active_tenant_id') ?? request()->attributes->get('resolved_tenant_id');
        $review = Review::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:published,pending,hidden',
        ]);

        $review->update(['status' => $validated['status']]);

        return back()->with('success', 'Review status updated.');
    }

    public function reply(Request $request, $id)
    {
        $tenantId = session('active_tenant_id') ?? request()->attributes->get('resolved_tenant_id');
        $review = Review::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'admin_reply' => 'nullable|string|max:2000',
        ]);

        $review->update(['admin_reply' => $validated['admin_reply']]);

        return back()->with('success', 'Reply saved successfully.');
    }

    public function destroy($id)
    {
        $tenantId = session('active_tenant_id') ?? request()->attributes->get('resolved_tenant_id');
        $review = Review::where('tenant_id', $tenantId)->findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
