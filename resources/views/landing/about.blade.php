@extends('layouts.landing')
@section('content')
  <style>
    .about-page-wrapper {
      overflow-x: hidden;
      width: 100%;
      max-width: 100vw;
      position: relative;
    }
    
    /* Ultra Premium 3D Hero */
    .about-hero {
      padding: 100px 8vw 40px !important;
      background: radial-gradient(circle at 50% 0%, rgba(67, 160, 71, 0.08), transparent 70%), var(--bg);
      position: relative;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 40px;
      align-items: center;
    }
    
    .about-hero-header {
      position: relative;
      z-index: 2;
    }
    
    .about-hero-header .eyebrow {
      display: inline-block;
      padding: 8px 16px;
      background: rgba(67, 160, 71, 0.1);
      color: var(--primary);
      border-radius: 30px;
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin-bottom: 24px;
    }
    
    .about-hero-header h1 {
      font-size: clamp(2.5rem, 4vw, 3.5rem);
      line-height: 1.1;
      margin-bottom: 24px;
      color: var(--dark);
      font-weight: 800;
      letter-spacing: -0.03em;
    }
    
    .about-hero-header h1 span {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    
    .about-hero-header p {
      font-size: 1.25rem;
      color: var(--muted);
      line-height: 1.7;
      max-width: 500px;
    }
    
    /* 3D Grocery Scene */
    .hero-3d-scene {
      position: relative;
      height: 500px;
      perspective: 1000px;
      z-index: 1;
    }
    
    .scene-card {
      position: absolute;
      border-radius: 24px;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.8);
      box-shadow: 
        -20px 20px 40px rgba(0, 0, 0, 0.08),
        inset 0 0 0 1px rgba(255, 255, 255, 0.5);
      padding: 24px;
      transform-style: preserve-3d;
      transition: transform 0.5s ease;
    }
    
    /* Center Main Card - Store Image */
    .card-main {
      width: 320px;
      height: 400px;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotateY(-15deg) rotateX(10deg);
      z-index: 3;
      padding: 12px;
      animation: floatMain 8s ease-in-out infinite alternate;
    }
    
    .card-main img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 16px;
    }
    
    /* Floating Grocery Elements */
    .grocery-orb {
      position: absolute;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #ffffff, #f3f4f6);
      box-shadow: 
        -10px 10px 20px rgba(0,0,0,0.1),
        inset 5px 5px 10px rgba(255,255,255,1),
        inset -5px -5px 15px rgba(0,0,0,0.05);
      font-size: 2.5rem;
      transform-style: preserve-3d;
    }
    
    .orb-1 {
      width: 100px;
      height: 100px;
      top: 10%;
      right: 15%;
      transform: translateZ(50px);
      animation: floatOrb 6s ease-in-out infinite alternate;
    }
    
    .orb-2 {
      width: 80px;
      height: 80px;
      bottom: 20%;
      left: 5%;
      transform: translateZ(80px);
      animation: floatOrb 7s ease-in-out infinite alternate-reverse;
      font-size: 2rem;
    }
    
    .orb-3 {
      width: 120px;
      height: 120px;
      bottom: 10%;
      right: 5%;
      transform: translateZ(120px);
      animation: floatOrb 9s ease-in-out infinite alternate;
      font-size: 3rem;
    }
    
    .card-floating-badge {
      position: absolute;
      top: 20%;
      left: -10%;
      background: var(--white);
      padding: 12px 20px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      gap: 12px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      transform: translateZ(60px);
      animation: floatBadge 8s ease-in-out infinite alternate;
    }
    
    .badge-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--primary);
      color: var(--white);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }
    
    .badge-text {
      font-weight: 700;
      color: var(--dark);
      font-size: 0.95rem;
    }
    
    .badge-sub {
      font-size: 0.8rem;
      color: var(--muted);
    }
    
    @keyframes floatMain {
      0% { transform: translate(-50%, -50%) rotateY(-15deg) rotateX(10deg) translateY(0); }
      100% { transform: translate(-50%, -50%) rotateY(-15deg) rotateX(10deg) translateY(-20px); }
    }
    
    @keyframes floatOrb {
      0% { transform: translateY(0) rotate(0deg); }
      100% { transform: translateY(-25px) rotate(10deg); }
    }
    
    @keyframes floatBadge {
      0% { transform: translateZ(60px) translateY(0); }
      100% { transform: translateZ(60px) translateY(15px); }
    }
    
    @media(max-width: 980px) {
      .about-hero {
        grid-template-columns: 1fr;
        padding-top: 120px;
        text-align: center;
      }
      .about-hero-header p {
        margin: 0 auto;
      }
      .hero-3d-scene {
        height: 400px;
        transform: scale(0.85);
        transform-origin: top center;
        margin-top: 20px;
      }
    }
    
    @media(max-width: 576px) {
      .hero-3d-scene {
        height: 300px;
        transform: scale(0.65);
        margin-top: 10px;
      }
    }
    
    .mission-section {
      padding: 120px 8vw;
      background: linear-gradient(180deg, var(--white) 0%, rgba(67, 160, 71, 0.03) 100%);
      position: relative;
    }
    
    .mission-container {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .mission-content h2 {
      font-size: clamp(2.5rem, 4vw, 3rem);
      margin-bottom: 24px;
      line-height: 1.1;
      font-weight: 800;
      color: var(--dark);
      letter-spacing: -0.02em;
    }
    
    .mission-content h2 span {
      position: relative;
      display: inline-block;
      color: var(--primary);
    }
    
    .mission-content p {
      font-size: 1.15rem;
      margin-bottom: 24px;
      color: var(--muted);
      line-height: 1.8;
    }
    
    .mission-image-wrapper {
      position: relative;
      border-radius: 24px;
      padding: 24px;
      background: var(--white);
      box-shadow: 0 30px 60px rgba(0,0,0,0.08);
    }
    
    .mission-image-wrapper::before {
      content: '';
      position: absolute;
      inset: -20px -20px 20px 20px;
      background: radial-gradient(circle at top right, rgba(67, 160, 71, 0.15), transparent 50%);
      border-radius: 40px;
      z-index: -1;
    }
    
    .mission-image {
      border-radius: 16px;
      overflow: hidden;
      position: relative;
    }
    
    .mission-image img {
      width: 100%;
      height: 400px;
      object-fit: cover;
      transition: transform 0.6s var(--ease);
    }
    
    .mission-image:hover img {
      transform: scale(1.05);
    }
    
    .mission-glass-card {
      position: absolute;
      bottom: -30px;
      left: -30px;
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.8);
      padding: 20px 24px;
      border-radius: 16px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      display: flex;
      align-items: center;
      gap: 16px;
      z-index: 2;
      animation: floatBadge 8s ease-in-out infinite alternate;
    }
    
    .mgc-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      background: rgba(46, 125, 50, 0.1);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
    }
    
    .mgc-text h4 {
      font-size: 1.1rem;
      margin-bottom: 4px;
    }
    
    .mgc-text p {
      font-size: 0.85rem;
      margin: 0;
      color: var(--muted);
    }
    
    @media(max-width: 980px) {
      .mission-container {
        grid-template-columns: 1fr;
      }
      .mission-glass-card {
        bottom: 20px;
        left: 20px;
      }
    }
    
    .values-section {
      padding: 120px 8vw;
      background: var(--bg);
    }
    
    .values-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 32px;
      margin-top: 60px;
    }
    
    .value-card {
      background: var(--white);
      padding: 40px;
      border-radius: var(--radius-lg);
      box-shadow: var(--shadow-sm);
      transition: transform 0.3s var(--ease), box-shadow 0.3s var(--ease);
      border: 1px solid var(--line);
    }
    
    .value-card:hover {
      transform: translateY(-8px);
      box-shadow: var(--shadow-md);
      border-color: rgba(46, 125, 50, 0.2);
    }
    
    .value-icon {
      width: 60px;
      height: 60px;
      border-radius: 16px;
      background: rgba(46, 125, 50, 0.1);
      color: var(--primary);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      margin-bottom: 24px;
    }
    
    .value-card h3 {
      font-size: 1.4rem;
      margin-bottom: 16px;
    }
    
    .stats-section {
      padding: 40px 8vw;
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: var(--white);
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      text-align: center;
    }
    
    .stat-item h4 {
      font-size: clamp(1.2rem, 3vw, 3.5rem);
      color: var(--white);
      margin-bottom: 8px;
    }
    
    .stat-item p {
      color: rgba(255, 255, 255, 0.8);
      font-size: clamp(0.6rem, 1.5vw, 1.1rem);
      font-weight: 500;
    }
    
    @media(max-width: 768px) {
      .stats-section {
        padding: 20px 1vw;
        gap: 2px;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        overflow: hidden;
      }
      .stat-item {
        padding: 0;
        min-width: 0;
        word-wrap: break-word;
        word-break: break-word;
      }
      .stat-item h4 {
        margin-bottom: 2px;
        font-size: 0.95rem !important;
      }
      .stat-item p {
        font-size: 0.55rem !important;
        line-height: 1.1;
      }
    }
  </style>

  <div class="about-page-wrapper">
  <section class="about-hero">
    <div class="about-hero-header fade-up">
      <span class="eyebrow">Our Story</span>
      <h1>Redefining the <br><span>Grocery Experience</span></h1>
      <p>Slot Store is the operating system for modern grocery. We're building the infrastructure that empowers grocers to run, scale, and transform their businesses in a digital-first world.</p>
    </div>
    
    <div class="hero-3d-scene fade-up" style="animation-delay: 0.2s;">
      <!-- Main 3D Card -->
      <div class="scene-card card-main">
        <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=1000" alt="Fresh Groceries">
      </div>
      
      <!-- 3D Grocery Orbs -->
      <div class="grocery-orb orb-1">🥑</div>
      <div class="grocery-orb orb-2">🛍️</div>
      <div class="grocery-orb orb-3">🍎</div>
      
      <!-- Floating Glass Badge -->
      <div class="card-floating-badge">
        <div class="badge-icon"><i class="fa-solid fa-bolt"></i></div>
        <div>
          <div class="badge-text">Ultra Fast</div>
          <div class="badge-sub">Commerce Engine</div>
        </div>
      </div>
    </div>
  </section>

  <section class="mission-section reveal">
    <div class="mission-container">
      <div class="mission-content">
        <span class="eyebrow">The Challenge</span>
        <h2>Built for the <br><span>Complexities</span> of Grocery</h2>
        <p>Traditional ecommerce platforms weren't built for the nuances of the grocery industry—inventory that spoils, weight-based pricing, regional variations, and complex fulfillment logistics.</p>
        <p>Slot Store was founded with a simple mission: to provide a platform built from the ground up to solve these exact challenges, giving you the powerful tools needed to compete and succeed.</p>
      </div>
      
      <div class="mission-image-wrapper">
        <div class="mission-image">
          <img src="{{ asset('images/grocery_tech_aisle.jpg') }}" alt="Modern Grocery Tech Aisle">
        </div>
        
        <div class="mission-glass-card">
          <div class="mgc-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
          <div class="mgc-text">
            <h4>Inventory Synced</h4>
            <p>100% Real-time accuracy</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="stats-section reveal">
    <div class="stat-item">
      <h4>500+</h4>
      <p>Stores Powered</p>
    </div>
    <div class="stat-item">
      <h4>₹2M+</h4>
      <p>Orders Processed</p>
    </div>
    <div class="stat-item">
      <h4>99.9%</h4>
      <p>Uptime SLA</p>
    </div>
    <div class="stat-item">
      <h4>24/7</h4>
      <p>Expert Support</p>
    </div>
  </section>

  <section class="values-section reveal">
    <div class="section-head">
      <span class="eyebrow">Our Core Values</span>
      <h2>What Drives Us Forward</h2>
      <p>The principles that guide our product development and customer relationships.</p>
    </div>
    
    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon"><i class="fa-solid fa-leaf"></i></div>
        <h3>Fresh Thinking</h3>
        <p>We continuously innovate our platform to keep you ahead of consumer trends and technological advancements in grocery retail.</p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fa-solid fa-handshake"></i></div>
        <h3>True Partnership</h3>
        <p>Your success is our success. We don't just provide software; we provide the support and expertise to help your business grow.</p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fa-solid fa-bolt"></i></div>
        <h3>Reliable Performance</h3>
        <p>Grocery doesn't stop, and neither do we. We ensure our platform is always fast, secure, and ready for your customers.</p>
      </div>
    </div>
  </section>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const reveals = document.querySelectorAll('.reveal');
      
      const revealOnScroll = () => {
        for (let i = 0; i < reveals.length; i++) {
          const windowHeight = window.innerHeight;
          const elementTop = reveals[i].getBoundingClientRect().top;
          const elementVisible = 100;
          
          if (elementTop < windowHeight - elementVisible) {
            reveals[i].classList.add('in-view');
          }
        }
      };
      
      window.addEventListener('scroll', revealOnScroll);
      revealOnScroll(); // Trigger once on load
    });
  </script>
@endsection
