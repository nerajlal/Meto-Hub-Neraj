@extends('layouts.landing')
@section('content')
  <section class="features" id="contact" style="padding-top: 150px;">
    <div class="section-head">
      <span class="eyebrow">Get in touch</span>
      <h2>Contact Us</h2>
      <p>Have questions? We'd love to hear from you.</p>
    </div>
    <div style="max-width: 600px; margin: 0 auto; background: var(--white); border-radius: var(--radius-md); padding: 40px; box-shadow: var(--shadow-sm); border: 1px solid var(--line);">
      <form class="newsletter" style="display: flex; flex-direction: column; gap: 16px;">
        <input type="text" placeholder="Your name" style="width: 100%; padding: 14px 20px; border-radius: 999px; border: 1px solid var(--line); font-family: inherit; font-size: 0.95rem;">
        <input type="email" placeholder="Your work email" required style="width: 100%; padding: 14px 20px; border-radius: 999px; border: 1px solid var(--line); font-family: inherit; font-size: 0.95rem;">
        <textarea placeholder="Your message" rows="5" style="width: 100%; padding: 14px 20px; border-radius: var(--radius-sm); border: 1px solid var(--line); font-family: inherit; font-size: 0.95rem; resize: vertical;"></textarea>
        <button type="button" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">Send Message</button>
      </form>
    </div>
  </section>
@endsection
