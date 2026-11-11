<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->get();
        return view('admin.settings.slider', compact('sliders'));
    }

    public function create()
    {
        return view('admin.settings.slider_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_desktop' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'image_mobile' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'nullable|string|max:255',
            'status' => 'boolean',
        ]);

        $desktopPath = $request->file('image_desktop')->store('sliders', 'public');
        $mobilePath = $request->file('image_mobile')->store('sliders', 'public');

        Slider::create([
            'image_desktop' => $desktopPath,
            'image_mobile' => $mobilePath,
            'title' => $request->title,
            'status' => $request->has('status'),
            'order' => Slider::count() + 1,
        ]);

        return redirect()->route('admin.settings.slider')->with('success', 'Slide added successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $slider = Slider::findOrFail($id);
        $request->validate(['status' => 'required|boolean']);
        $slider->update(['status' => $request->boolean('status')]);

        if ($request->expectsJson()) return response()->json(['status' => $slider->status]);
        return redirect()->route('admin.settings.slider')->with('success', $slider->status ? 'Slide enabled.' : 'Slide hidden from storefront.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'exists:sliders,id',
        ]);

        foreach ($request->order as $index => $id) {
            Slider::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail($id);
        
        if ($slider->image_desktop) {
            Storage::disk('public')->delete($slider->image_desktop);
        }
        if ($slider->image_mobile) {
            Storage::disk('public')->delete($slider->image_mobile);
        }

        $slider->delete();

        return redirect()->route('admin.settings.slider')->with('success', 'Slide deleted successfully.');
    }
}
