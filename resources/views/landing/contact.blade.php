@extends('layouts.landing')
@section('content')
  <style>
    :root {
        --v2-primary: #4F46E5;
        --v2-secondary: #7C3AED;
        --v2-accent: #EC4899;
        --v2-dark: #0F172A;
        --v2-text: #334155;
        --v2-light: #F8FAFC;
        --v2-muted: #94A3B8;
        --v2-border: #E2E8F0;
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
      background: radial-gradient(circle, rgba(79, 70, 229, 0.08) 0%, transparent 60%);
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
      background: rgba(79, 70, 229, 0.1);
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
      background: rgba(79, 70, 229, 0.2);
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
      box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
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
      box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
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
              <p>support@metohub.com</p>
            </div>
          </div>
          <div class="method-item">
            <div class="method-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="method-text">
              <h4>Call Us</h4>
              <p>+91 (800) 123-4567</p>
            </div>
          </div>
          <div class="method-item">
            <div class="method-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="method-text">
              <h4>Visit Us</h4>
              <p>123 Innovation Drive, Tech Park<br>Bangalore, IN 560001</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side Form -->
      <div class="contact-form-card">
        <form>
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" class="form-control" placeholder="John Doe" required>
          </div>
          
          <div class="form-group">
            <label>Work Email</label>
            <input type="email" class="form-control" placeholder="john@company.com" required>
          </div>
          
          <div class="form-group">
            <label>Subject</label>
            <select class="form-control">
              <option>General Inquiry</option>
              <option>Sales & Pricing</option>
              <option>Technical Support</option>
              <option>Partnerships</option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Message</label>
            <textarea class="form-control" placeholder="How can we help you scale your business?"></textarea>
          </div>
          
          <button type="button" class="submit-btn">
            Send Message <i class="fa-solid fa-paper-plane"></i>
          </button>
        </form>
      </div>
    </div>
  </div>
@endsection
