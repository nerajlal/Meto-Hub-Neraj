<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\GlobalProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GlobalImageController extends Controller
{
    public function index()
    {
        $images = GlobalProductImage::orderBy('title')->get();
        return view('super_admin.global_images.index', compact('images'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:global_product_images',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('image')->store('global_product_images', 'public');

        GlobalProductImage::create([
            'title' => strtolower(trim($request->title)),
            'image_path' => $path,
            'status' => true
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
}
