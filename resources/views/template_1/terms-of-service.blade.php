@extends('template_1.layouts.app')

@section('title', 'Terms of Service | ' . ($currentTenant->name ?? 'SaaS Store'))

@section('content')
<div style="max-width: 800px; margin: 0 auto; padding: 3rem 1.5rem; background: #fff; border-radius: 1.5rem; border: 1px solid var(--border-color);">
    <h1 style="font-weight: 800; font-size: 2.2rem; color: var(--primary-color); margin-bottom: 1.5rem;">Terms of Service</h1>
    @if($currentTenant->terms_of_service)
        <div style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6;">
            {!! nl2br(e($currentTenant->terms_of_service)) !!}
        </div>
    @else
        <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 2rem;">Welcome to {{ $currentTenant->name ?? 'SaaS Store' }}. By accessing or using our storefront website, you agree to comply with and be bound by these terms.</p>
        
        <h2 style="font-weight: 700; font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem;">Use of the Site</h2>
        <p style="color: var(--primary-color); line-height: 1.6;">You must be at least 18 years of age or accessing the site under the supervision of a parent or guardian. You are responsible for maintaining account confidentiality.</p>

        <h2 style="font-weight: 700; font-size: 1.4rem; margin-top: 2rem; margin-bottom: 1rem;">Pricing and Availability</h2>
        <p style="color: var(--primary-color); line-height: 1.6;">All prices listed on our site are inclusive of tax where applicable. We reserve the right to modify prices or limit quantities at any time without prior notice.</p>
    @endif
</div>
@endsection
