@extends('layouts.landing')
@section('content')
  <section class="features" id="contact" style="padding-bottom: 100px;">
    <div class="section-head">
      <span class="eyebrow">Get in touch</span>
      <h2>Contact Us</h2>
      <p>Have questions? We'd love to hear from you. Fill out the form below and our team will get back to you shortly.</p>
    </div>
    <div style="max-width: 600px; margin: 0 auto; background: #1F2937; border-radius: 16px; padding: 40px; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
      <form class="newsletter" style="display: flex; flex-direction: column; gap: 20px;">
        <div>
          <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-muted, #a1a1aa);">Name</label>
          <input type="text" placeholder="John Doe" style="width: 100%; padding: 14px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: #fff; font-family: inherit; font-size: 0.95rem;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-muted, #a1a1aa);">Work Email</label>
          <input type="email" placeholder="john@company.com" required style="width: 100%; padding: 14px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: #fff; font-family: inherit; font-size: 0.95rem;">
        </div>
        <div>
          <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; color: var(--text-muted, #a1a1aa);">Message</label>
          <textarea placeholder="How can we help you?" rows="5" style="width: 100%; padding: 14px 20px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.2); color: #fff; font-family: inherit; font-size: 0.95rem; resize: vertical;"></textarea>
        </div>
        <button type="button" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">Send Message</button>
      </form>
    </div>
  </section>
@endsection
