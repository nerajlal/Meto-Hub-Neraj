<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LandingController extends Controller
{
    public function homepage()
    {
        $profiles = config('demo_storefronts', []);
        $tenants = \App\Models\Tenant::whereIn('slug', array_keys($profiles))->get()->keyBy('slug');
        $demos = collect($profiles)->map(function ($profile, $slug) use ($tenants) {
            $tenant = $tenants->get($slug);
            return $tenant ? $profile + ['tenant' => $tenant] : null;
        })->filter();
        $landingConfig = [
            'plans' => config('saas.plans'), 'trialDays' => config('saas.trial_days'),
            'register' => route('saas.register'), 'resend' => route('saas.verification.resend'),
        ];

        $reviews = collect(config('landing_reviews', []))->filter(function ($review) {
            return is_array($review) && (($review['approved'] ?? false) === true || ($review['sample'] ?? false) === true)
                && is_string($review['quote'] ?? null) && trim($review['quote']) !== ''
                && is_string($review['name'] ?? null) && trim($review['name']) !== '';
        });

        return view('landing.new-landing1', compact('demos', 'landingConfig', 'reviews'));
    }

    public function index()
    {
        return redirect()->route('landing', request()->query(), 301);
    }

    public function templates()
    {
        return redirect()->route('landing.themes', request()->query(), 301);
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
    public function blogs(\App\Services\BlogFeed $feed)
    {
        $result = $feed->load();
        return response()->view('landing.blogs.index', [
            'blogs' => $result['blogs'], 'feedState' => $result['state'],
        ], $result['state'] === 'unavailable' ? 503 : 200);
    }

    public function blogShow($id, \App\Services\BlogFeed $feed)
    {
        abort_unless(ctype_digit((string) $id) && (int) $id > 0, 404);
        if ((string) (int) $id !== (string) $id) {
            return redirect()->route('landing.blog.show', ['id' => (int) $id] + request()->query(), 301);
        }
        $result = $feed->load();
        if ($result['state'] === 'unavailable') {
            return response()->view('landing.blogs.index', ['blogs' => [], 'feedState' => 'unavailable'], 503);
        }
        $blog = collect($result['blogs'])->firstWhere('id', (int) $id);
        abort_unless($blog, 404);
        $recentBlogs = collect($result['blogs'])->where('id', '!=', (int) $id)->take(3);
        return view('landing.blogs.show', ['blog' => $blog, 'recentBlogs' => $recentBlogs, 'feedState' => $result['state']]);
    }
}
