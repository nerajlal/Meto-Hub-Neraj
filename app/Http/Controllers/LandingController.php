<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.new-landing');
    }

    public function templates()
    {
        return view('landing.templates');
    }

    public function features()
    {
        return view('landing.features');
    }
    public function solutions()
    {
        return view('landing.solutions');
    }
    public function pricing()
    {
        return view('landing.pricing');
    }
    public function themes()
    {
        return view('landing.themes');
    }
    public function analytics()
    {
        return view('landing.analytics');
    }
    public function contact()
    {
        return view('landing.contact');
    }
    public function about()
    {
        return view('landing.about');
    }
    public function privacy()
    {
        return view('landing.privacy');
    }
    public function terms()
    {
        return view('landing.terms');
    }
    
    public function refund()
    {
        return view('landing.refund');
    }

    public function careers() { return view('landing.careers'); }
    public function press() { return view('landing.press'); }
    public function independentGrocers() { return view('landing.independent-grocers'); }
    public function groceryChains() { return view('landing.grocery-chains'); }
    public function farmersMarkets() { return view('landing.farmers-markets'); }
    public function helpCenter() { return view('landing.help-center'); }
    public function apiDocs() { return view('landing.api-docs'); }
    public function guides() { return view('landing.guides'); }
    public function security() { return view('landing.security'); }

    public function handleDemoRequest(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'business_name' => 'required|string|max:255',
            'target_url' => 'nullable|string'
        ]);

        try {
            $adminEmail = config('app.admin_email');
            $senderEmail = config('mail.from.address');
            $appName = config('app.name');

            $messageContent = "New Demo Request Received for {$appName}:\n\n" .
                "Email: {$validated['email']}\n" .
                "Business Name: {$validated['business_name']}\n" .
                "Requested Demo: {$validated['target_url']}\n\n" .
                "Timestamp: " . now()->format('Y-m-d H:i:s');

            Mail::raw($messageContent, function ($message) use ($adminEmail, $appName) {
                $message->to($adminEmail)
                    ->subject("New Lead: Demo Request from {$appName}");
            });

            return response()->json(['success' => true, 'message' => 'Request submitted successfully.']);
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
            // We still return success to the user so they can see the demo, 
            // but log the error for the developer.
            return response()->json(['success' => true, 'message' => 'Request processed (Mail logged).']);
        }
    }
    public function blogs()
    {
        $response = Http::withHeaders([
            'x-api-key' => 'pk_niVQ6VEvYX6vs9iWkh4rNMTtsb1AGgfU'
        ])->get('https://blogs.task19.com/api/v1/projects/blogs');

        $blogs = $response->successful() ? $response->json('blogs') : [];

        return view('landing.blogs.index', compact('blogs'));
    }

    public function blogShow($id)
    {
        $response = Http::withHeaders([
            'x-api-key' => 'pk_niVQ6VEvYX6vs9iWkh4rNMTtsb1AGgfU'
        ])->get('https://blogs.task19.com/api/v1/projects/blogs');

        $blogs = $response->successful() ? $response->json('blogs') : [];
        $blog = collect($blogs)->firstWhere('id', (int) $id);

        if (!$blog) {
            abort(404);
        }

        $recentBlogs = collect($blogs)->where('id', '!=', (int) $id)->take(3);

        return view('landing.blogs.show', compact('blog', 'recentBlogs'));
    }
}
