@extends('layouts.landing')
@section('content')
  <style>
    :root {
        --v2-primary: #244a3b;
        --v2-secondary: #54705c;
        --v2-accent: #8c6843;
        --v2-dark: #282c28;
        --v2-text: #6f6b63;
        --v2-light: #fbfaf7;
        --v2-muted: #8c857a;
        --v2-border: #e4ded3;
    }
    
    .contact-page-wrapper {
      background-color: var(--v2-light);
      min-height: 100vh;
      padding-top: 160px;
      padding-bottom: 120px;
      overflow: hidden;
      position: relative;
    }

    .contact-page-wrapper::before {
      content: '';
      position: absolute;
      top: -10%;
      right: -5%;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle, rgba(36, 74, 59, 0.08) 0%, transparent 60%);
      z-index: 0;
    }
    
    .contact-container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 5%;
      position: relative;
      z-index: 1;
      display: grid;
      grid-template-columns: 1fr 1.2fr;
      gap: 60px;
      align-items: center;
    }

    /* Left Side Info */
    .contact-info {
      padding-right: 40px;
    }

    .contact-info .eyebrow {
      display: inline-block;
      padding: 8px 16px;
      background: rgba(36, 74, 59, 0.1);
      color: var(--v2-primary);
      border-radius: 30px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 24px;
    }

    .contact-info h1 {
      font-size: clamp(2.5rem, 4vw, 3.5rem);
      line-height: 1.1;
      margin-bottom: 24px;
      color: var(--v2-dark);
      font-weight: 800;
      letter-spacing: -0.03em;
    }
    
    .contact-info h1 span {
      background: linear-gradient(135deg, var(--v2-primary), var(--v2-accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .contact-info p {
      font-size: 1.15rem;
      color: var(--v2-text);
      line-height: 1.7;
      margin-bottom: 40px;
    }

    .contact-methods {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }

    .method-item {
      display: flex;
      align-items: flex-start;
      gap: 20px;
    }

    .method-icon {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: var(--v2-primary);
      box-shadow: 0 10px 20px rgba(0,0,0,0.05);
      flex-shrink: 0;
    }

    .method-text h4 {
      font-size: 1.1rem;
      color: var(--v2-dark);
      margin-bottom: 4px;
    }
    
    .method-text p {
      margin: 0;
      font-size: 0.95rem;
      color: var(--v2-muted);
    }
    
    /* Right Side Form */
    .contact-form-card {
      background: #fff;
      border-radius: 24px;
      padding: 40px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.06);
      border: 1px solid var(--v2-border);
      position: relative;
    }

    .contact-form-card::after {
      content: '';
      position: absolute;
      bottom: -20px;
      left: 10%;
      width: 80%;
      height: 20px;
      background: rgba(36, 74, 59, 0.2);
      filter: blur(20px);
      z-index: -1;
      border-radius: 100%;
    }

    .form-group {
      margin-bottom: 24px;
    }

    .form-group label {
      display: block;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--v2-text);
      margin-bottom: 8px;
    }

    .form-control {
      width: 100%;
      padding: 14px 20px;
      border-radius: 12px;
      border: 1px solid var(--v2-border);
      background: var(--v2-light);
      color: var(--v2-dark);
      font-size: 1rem;
      transition: all 0.3s ease;
      font-family: inherit;
    }

    .form-control:focus {
      outline: none;
      border-color: var(--v2-primary);
      background: #fff;
      box-shadow: 0 0 0 4px rgba(36, 74, 59, 0.1);
    }

    textarea.form-control {
      resize: vertical;
      min-height: 120px;
    }

    .submit-btn {
      width: 100%;
      padding: 16px 30px;
      border-radius: 12px;
      background: var(--v2-dark);
      color: #fff;
      font-weight: 600;
      font-size: 1.05rem;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: all 0.3s ease;
    }

    .submit-btn:hover {
      background: var(--v2-primary);
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(36, 74, 59, 0.2);
    }

    @media (max-width: 980px) {
      .contact-container {
        grid-template-columns: 1fr;
        gap: 50px;
      }
      .contact-info {
        padding-right: 0;
        text-align: center;
      }
      .contact-methods {
        align-items: center;
        text-align: left;
      }
      .contact-page-wrapper {
        padding-top: 120px;
      }
    }
  </style>

  <div class="contact-page-wrapper">
    <div class="contact-container">
      <!-- Left Side -->
      <div class="contact-info">
        <span class="eyebrow">Get In Touch</span>
        <h1>Let's build your <br><span>Dream Store</span></h1>
        <p>Whether you're looking to start a new business or migrate an existing one, our expert team is here to help you scale.</p>
        
        <div class="contact-methods">
          <div class="method-item">
            <div class="method-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="method-text">
              <h4>Email Us</h4>
              <p><a href="mailto:{{ config('marketing.contact.email') }}">{{ config('marketing.contact.email') }}</a></p>
            </div>
          </div>
          <div class="method-item">
            <div class="method-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="method-text">
              <h4>Call Us</h4>
              <p><a href="tel:{{ config('marketing.contact.phone_uri') }}">{{ config('marketing.contact.phone') }}</a></p>
            </div>
          </div>
          <div class="method-item">
            <div class="method-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="method-text">
              <h4>Visit Us</h4>
              <p>{{ config('marketing.contact.location') }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side Form -->
      <div class="contact-form-card">
        @if(session('contact_sent'))<p role="status" class="contact-form-success">{{ session('contact_sent') }}</p>@endif
        @if($errors->any())<div role="alert" class="contact-form-errors"><p>Please check your message:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('landing.contact.submit') }}">
          @csrf
          <div class="contact-honeypot" aria-hidden="true"><label for="contact_website">Leave this field empty</label><input id="contact_website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
          <div class="form-group"><label for="contact_name">Full Name</label><input id="contact_name" name="name" type="text" class="form-control" autocomplete="name" maxlength="100" value="{{ old('name') }}" required></div>
          <div class="form-group"><label for="contact_email">Email</label><input id="contact_email" name="email" type="email" class="form-control" autocomplete="email" maxlength="254" value="{{ old('email') }}" required></div>
          <div class="form-group"><label for="contact_subject">Subject</label><select id="contact_subject" name="subject" class="form-control">@foreach(['General Inquiry','Sales & Pricing','Technical Support','Partnerships'] as $subject)<option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>@endforeach</select></div>
          <div class="form-group"><label for="contact_message">Message</label><textarea id="contact_message" name="message" class="form-control" maxlength="5000" placeholder="Tell us about your store or question." required>{{ old('message') }}</textarea></div>
          <label class="contact-consent" for="contact_consent"><input id="contact_consent" type="checkbox" name="consent" value="1" @checked(old('consent')) required><span>I have read the <a href="{{ route('landing.privacy') }}">Privacy Policy</a> and agree to being contacted about this enquiry.</span></label>
          <p class="contact-form-note">Please don’t include passwords, card details or other sensitive information.</p>
          <button type="submit" class="submit-btn">Send Message <i class="fa-solid fa-paper-plane" aria-hidden="true"></i></button>
        </form>
      </div>
    </div>
  </div>
@endsection
