@extends('template_1.layouts.app')

@section('title', 'Contact Us | ' . ($currentTenant->name ?? 'SaaS Store'))

@section('content')
<div class="static-page-container">
    <h1 class="page-title">Get in Touch</h1>
    
    <div class="contact-grid">
        <div class="contact-info">
            <h2>Support</h2>
            <p>Have a question about an order or our fresh produce? Our team is here to help.</p>
            
            <div class="info-item">
                <i class="fa-solid fa-envelope"></i>
                <span>{{ $currentTenant->contact_email ?? 'support@task19.com' }}</span>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-phone"></i>
                <span>{{ $currentTenant->contact_phone ?? '+91 98765 43210' }}</span>
            </div>
            <div class="info-item">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $currentTenant->contact_address ?? 'Fresh Grocery, MG Road, Bangalore, India' }}</span>
            </div>
            @if(!empty($currentTenant->whatsapp_number))
            <div class="info-item">
                <i class="fa-brands fa-whatsapp"></i>
                <span>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $currentTenant->whatsapp_number) }}" target="_blank" style="color: inherit; text-decoration: none;">
                        {{ $currentTenant->whatsapp_number }}
                    </a>
                </span>
            </div>
            @endif
        </div>

        <form class="contact-form">
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" placeholder="Enter your name">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea rows="5" placeholder="How can we help?"></textarea>
            </div>
            <button type="submit" class="btn-primary">Send Message</button>
        </form>
    </div>
</div>

<style>
    .static-page-container { max-width: 1000px; margin: 0 auto; padding: 3rem 0; }
    .page-title { font-size: 2.5rem; font-weight: 700; margin-bottom: 3rem; text-align: center; }
    
    .contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; }
    .contact-info h2 { font-size: 1.5rem; margin-bottom: 1rem; }
    .contact-info p { color: var(--text-muted); margin-bottom: 2rem; }
    
    .info-item { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; font-size: 1.1rem; }
    .info-item i { color: var(--accent-color); font-size: 1.25rem; }

    .contact-form { display: flex; flex-direction: column; gap: 1.5rem; }
    .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; }
    .form-group input, .form-group textarea { width: 100%; padding: 0.85rem 1rem; border: 1px solid var(--border-color); border-radius: 0.75rem; font-family: inherit; }
    
    @media (max-width: 768px) {
        .contact-grid { grid-template-columns: 1fr; gap: 3rem; }
    }
</style>
@endsection
