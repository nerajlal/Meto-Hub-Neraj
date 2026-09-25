<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\Product;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReelController extends Controller
{
    public function index()
    {
        $reels = Reel::with(['product', 'collection'])->orderBy('order')->get();
        return view('admin.settings.reels', compact('reels'));
    }

    public function create()
    {
        $products = Product::where('status', 'active')->get();
        $collections = Collection::where('status', 1)->get();
        return view('admin.settings.reel_create', compact('products', 'collections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instagram_url' => 'required|url',
            'title' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_type' => 'required|in:product,collection',
            'product_id' => 'nullable|required_if:link_type,product|exists:products,id',
            'collection_id' => 'nullable|required_if:link_type,collection|exists:collections,id',
            'status' => 'boolean',
        ]);

        $data = [
            'instagram_url' => $request->instagram_url,
            'title' => $request->title,
            'link_type' => $request->link_type,
            'product_id' => $request->link_type === 'product' ? $request->product_id : null,
            'collection_id' => $request->link_type === 'collection' ? $request->collection_id : null,
            'status' => $request->has('status'),
            'order' => Reel::count() + 1,
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('reels', 'public');
        }

        Reel::create($data);

        return redirect()->route('admin.settings.reels')->with('success', 'Reel added successfully.');
    }

    public function edit($id)
    {
        $reel = Reel::findOrFail($id);
        $products = Product::where('status', 'active')->get();
        $collections = Collection::where('status', 1)->get();
        return view('admin.settings.reel_edit', compact('reel', 'products', 'collections'));
    }

    public function update(Request $request, $id)
    {
        $reel = Reel::findOrFail($id);

        $request->validate([
            'instagram_url' => 'required|url',
            'title' => 'nullable|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'link_type' => 'required|in:product,collection',
            'product_id' => 'nullable|required_if:link_type,product|exists:products,id',
            'collection_id' => 'nullable|required_if:link_type,collection|exists:collections,id',
            'status' => 'boolean',
        ]);

        $data = [
            'instagram_url' => $request->instagram_url,
            'title' => $request->title,
            'link_type' => $request->link_type,
            'product_id' => $request->link_type === 'product' ? $request->product_id : null,
            'collection_id' => $request->link_type === 'collection' ? $request->collection_id : null,
            'status' => $request->has('status'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($reel->thumbnail) {
                Storage::disk('public')->delete($reel->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('reels', 'public');
        }

        $reel->update($data);

        return redirect()->route('admin.settings.reels')->with('success', 'Reel updated successfully.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:reels,id',
        ]);

        foreach ($request->order as $index => $id) {
            Reel::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $reel = Reel::findOrFail($id);

        if ($reel->thumbnail) {
            Storage::disk('public')->delete($reel->thumbnail);
        }

        $reel->delete();

        return redirect()->route('admin.settings.reels')->with('success', 'Reel deleted successfully.');
    }
}
