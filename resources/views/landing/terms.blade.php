@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px;">
    <div class="section-head">
      <span class="eyebrow">Legal</span>
      <h2>Terms of Service</h2>
      <p>Last updated: {{ now()->format('F j, Y') }}</p>
    </div>
    <div style="max-width: 800px; margin: 0 auto; line-height: 1.8; color: var(--muted); background: var(--white); padding: 40px; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--line);">
      <h3 style="margin-bottom: 16px; font-size: 1.4rem;">1. Agreement to Terms</h3>
      <p style="margin-bottom: 24px;">By accessing our website, you agree to be bound by these Terms of Service and to comply with all applicable laws and regulations.</p>
      
      <h3 style="margin-bottom: 16px; font-size: 1.4rem;">2. Use License</h3>
      <p style="margin-bottom: 24px;">Permission is granted to temporarily download one copy of the materials on Slot Store's website for personal, non-commercial transitory viewing only.</p>
      
      <h3 style="margin-bottom: 16px; font-size: 1.4rem;">3. Disclaimer</h3>
      <p style="margin-bottom: 24px;">The materials on Slot Store's website are provided on an 'as is' basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties.</p>
    </div>
  </section>
@endsection
