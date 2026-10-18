<?php

namespace App\Http\Controllers;

use App\Mail\ContactEnquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:254',
            'subject' => ['required', Rule::in(['General Inquiry', 'Sales & Pricing', 'Technical Support', 'Partnerships'])],
            'message' => 'required|string|max:5000',
            'consent' => 'accepted',
            'website' => 'nullable|string|max:0',
        ]);
        try {
            Mail::to(config('marketing.contact.email'))->send(new ContactEnquiry(
                preg_replace('/[\r\n]+/', ' ', trim($data['name'])), $data['email'], $data['subject'], $data['message']
            ));
        } catch (\Throwable $error) {
            report($error);

            return back()->withErrors(['contact' => 'Your message could not be sent. Please email or call us directly.'])->withInput($request->except('website'));
        }

        return redirect()->route('landing.contact')->with('contact_sent', 'Your message has been sent. Thank you for getting in touch.');
    }
}
