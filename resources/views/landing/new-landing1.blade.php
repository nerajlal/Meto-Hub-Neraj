@extends('layouts.landing')
@section('content')
    <style>
        /* =========================================================
           MetoHub V2 - Premium Landing Redesign
           ========================================================= */
        :root {
            --v2-primary: #4F46E5;
            --v2-secondary: #7C3AED;
            --v2-accent: #EC4899;
            --v2-dark: #0F172A;
            --v2-text: #334155;
            --v2-light: #F8FAFC;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
        }

        body {
            background-color: var(--v2-light);
        }

        /* Animations */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(2deg);
            }
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Hero Section */
        .hero-v2 {
            position: relative;
            padding: 160px 5% 100px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 95vh;
            background: radial-gradient(circle at top left, rgba(79, 70, 229, 0.05), transparent 40%),
                radial-gradient(circle at bottom right, rgba(236, 72, 153, 0.05), transparent 40%);
        }

        .hero-v2-mesh {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(79, 70, 229, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(79, 70, 229, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 0;
        }

        .hero-v2-blob {
            position: absolute;
            filter: blur(80px);
            z-index: 0;
            opacity: 0.6;
            animation: float 10s ease-in-out infinite;
        }

        .hero-v2-blob-1 {
            top: 10%;
            right: 10%;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, var(--v2-primary), var(--v2-secondary));
            border-radius: 50%;
        }

        .hero-v2-blob-2 {
            bottom: -50px;
            left: 10%;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, var(--v2-accent), var(--v2-secondary));
            border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
            animation-duration: 14s;
        }

        .hero-v2-content {
            position: relative;
            z-index: 2;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        @media (max-width: 992px) {
            .hero-v2-content {
                grid-template-columns: 1fr;
                text-align: center;
            }
        }

        .hero-v2-text {
            animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .v2-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--v2-primary);
            border: 1px solid rgba(79, 70, 229, 0.2);
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 24px;
            backdrop-filter: blur(10px);
        }

        .hero-v2-text h1 {
            font-size: clamp(2.5rem, 5vw, 4rem);
            line-height: 1.1;
            color: var(--v2-dark);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
        }

        .text-gradient {
            background: linear-gradient(to right, var(--v2-primary), var(--v2-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-size: 200% auto;
            animation: gradientShift 5s ease infinite;
        }

        .hero-v2-text p {
            font-size: 1.15rem;
            color: var(--v2-text);
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 90%;
        }

        @media (max-width: 992px) {
            .hero-v2-text p {
                margin: 0 auto 40px;
            }
        }

        .v2-cta-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        @media (max-width: 992px) {
            .v2-cta-group {
                justify-content: center;
            }
        }

        .v2-btn {
            padding: 16px 32px;
            border-radius: 999px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            text-decoration: none;
        }

        .v2-btn-primary {
            background: linear-gradient(135deg, var(--v2-primary), var(--v2-secondary));
            color: #fff;
            box-shadow: 0 10px 25px rgba(79, 70, 229, 0.3);
            border: none;
        }

        .v2-btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(79, 70, 229, 0.4);
            color: #fff;
        }

        .v2-btn-outline {
            background: var(--glass-bg);
            color: var(--v2-dark);
            border: 2px solid rgba(15, 23, 42, 0.1);
            backdrop-filter: blur(10px);
        }

        .v2-btn-outline:hover {
            border-color: var(--v2-primary);
            color: var(--v2-primary);
            transform: translateY(-3px);
        }

        /* Glassmorphism Mockup */
        .hero-v2-visual {
            position: relative;
            animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) 0.2s forwards;
            opacity: 0;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
            transition: transform 0.5s ease;
        }

        .hero-v2-visual:hover .glass-card {
            transform: perspective(1000px) rotateY(0deg) rotateX(0deg);
        }

        .mockup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .mockup-dots {
            display: flex;
            gap: 6px;
        }

        .mockup-dots span {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .mockup-dots span:nth-child(1) {
            background: #FF5F56;
        }

        .mockup-dots span:nth-child(2) {
            background: #FFBD2E;
        }

        .mockup-dots span:nth-child(3) {
            background: #27C93F;
        }

        .mockup-title {
            font-weight: 700;
            color: var(--v2-dark);
            font-size: 0.9rem;
        }

        .mockup-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .mockup-stat {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .mockup-stat:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.03);
        }

        .stat-label {
            color: var(--v2-text);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
            display: block;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--v2-dark);
        }

        .stat-value.gradient {
            background: linear-gradient(to right, var(--v2-primary), var(--v2-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .floating-element {
            position: absolute;
            background: #fff;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 16px;
            animation: float 6s ease-in-out infinite;
            z-index: 10;
        }

        .floating-1 {
            top: -20px;
            right: -20px;
            animation-delay: 0s;
        }

        .floating-2 {
            bottom: -30px;
            left: -30px;
            animation-delay: 2s;
        }

        .float-icon {
            width: 40px;
            height: 40px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--v2-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .float-text strong {
            display: block;
            color: var(--v2-dark);
            font-size: 0.95rem;
        }

        .float-text small {
            color: var(--v2-text);
            font-size: 0.8rem;
        }

        /* Features Section - Simple Layout */
        .features-v2 {
            padding: 100px 5%;
            background: #fff;
        }

        .section-title {
            text-align: center;
            max-width: 600px;
            margin: 0 auto 60px;
        }

        .section-title h2 {
            font-size: clamp(2rem, 4vw, 2.5rem);
            color: var(--v2-dark);
            font-weight: 800;
            margin-bottom: 16px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: var(--v2-text);
        }

        .simple-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .simple-feature {
            text-align: center;
            padding: 20px;
        }

        .simple-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: rgba(79, 70, 229, 0.1);
            color: var(--v2-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .simple-feature h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--v2-dark);
            margin-bottom: 12px;
        }

        .simple-feature p {
            font-size: 1rem;
            color: var(--v2-text);
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .simple-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Pricing Section */
        .pricing-v2 {
            padding: 100px 5%;
            background: var(--v2-light);
        }

        .pricing-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .pricing-card {
            background: #fff;
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .pricing-card.popular {
            border: 2px solid var(--v2-primary);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--v2-primary), var(--v2-secondary));
            color: #fff;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .plan-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--v2-text);
            margin-bottom: 10px;
        }

        .plan-price {
            font-size: 3rem;
            font-weight: 800;
            color: var(--v2-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: baseline;
        }

        .plan-price span {
            font-size: 1rem;
            color: var(--v2-text);
            font-weight: 500;
            margin-left: 5px;
        }

        .plan-desc {
            color: var(--v2-text);
            font-size: 0.95rem;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .plan-features {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
            flex: 1;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 0.95rem;
            color: var(--v2-dark);
        }

        .plan-features li i {
            color: #10B981;
            font-size: 1.1rem;
        }

        .plan-btn {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }

        .plan-btn-outline {
            background: #fff;
            color: var(--v2-primary);
            border: 2px solid var(--v2-primary);
        }

        .plan-btn-outline:hover {
            background: rgba(79, 70, 229, 0.05);
        }

        .plan-btn-primary {
            background: var(--v2-primary);
            color: #fff;
            border: 2px solid var(--v2-primary);
        }

        .plan-btn-primary:hover {
            background: var(--v2-secondary);
            border-color: var(--v2-secondary);
            color: #fff;
        }

        @media (max-width: 992px) {
            .pricing-grid {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
        }
    </style>

    <!-- ================= HERO V2 ================= -->
    <section class="hero-v2">
        <div class="hero-v2-mesh"></div>
        <div class="hero-v2-blob hero-v2-blob-1"></div>
        <div class="hero-v2-blob hero-v2-blob-2"></div>

        <div class="hero-v2-content">
            <div class="hero-v2-text">
                <div class="v2-badge">
                    <i class="fa-solid fa-sparkles"></i> MetoHub v2.0 is Here
                </div>
                <h1>Build your dream store <span class="text-gradient">for just ₹99.</span></h1>
                <p>Experience the most advanced eCommerce website builder. Design breathtaking storefronts, manage
                    inventory, and scale globally without writing a single line of code.</p>

                <div class="v2-cta-group">
                    <a href="javascript:void(0)" class="v2-btn v2-btn-primary">
                        Start Building Free <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="#" class="v2-btn v2-btn-outline">
                        <i class="fa-solid fa-play"></i> Watch Demo
                    </a>
                </div>
                <p style="margin-top: 20px; font-size: 1.15rem; color: var(--v2-dark); font-weight: 600;">Start your online store instantly, at just <span style="color: var(--v2-primary); font-size: 1.3rem;">₹99</span> 😮</p>
            </div>

            <div class="hero-v2-visual">
                <div class="floating-element floating-1">
                    <div class="float-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                    <div class="float-text">
                        <strong>New Sale!</strong>
                        <small>₹299.00 via Stripe</small>
                    </div>
                </div>

                <div class="floating-element floating-2">
                    <div class="float-icon" style="color: var(--v2-accent); background: rgba(236,72,153,0.1);"><i
                            class="fa-solid fa-chart-line"></i></div>
                    <div class="float-text">
                        <strong>+24% Traffic</strong>
                        <small>Last 7 days</small>
                    </div>
                </div>

                <div class="glass-card">
                    <div class="mockup-header">
                        <div class="mockup-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <div class="mockup-title">metohub.com/dashboard</div>
                        <i class="fa-solid fa-bars text-muted"></i>
                    </div>

                    <div class="mockup-grid">
                        <div class="mockup-stat">
                            <span class="stat-label">Total Revenue</span>
                            <span class="stat-value gradient">₹84,290</span>
                        </div>
                        <div class="mockup-stat">
                            <span class="stat-label">Active Users</span>
                            <span class="stat-value">1,204</span>
                        </div>
                        <div class="mockup-stat">
                            <span class="stat-label">Conversion</span>
                            <span class="stat-value text-gradient">4.8%</span>
                        </div>
                        <div class="mockup-stat">
                            <span class="stat-label">Products</span>
                            <span class="stat-value">842</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= FEATURES SIMPLE LAYOUT ================= -->
    <section class="features-v2">
        <div class="section-title">
            <h2>Everything you need to scale</h2>
            <p>A unified platform that combines breathtaking design with powerful backend logistics.</p>
        </div>

        <div class="simple-grid">
            <div class="simple-feature">
                <div class="simple-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                <h3>Dynamic Theming</h3>
                <p>Switch between professionally designed, high-converting templates instantly without touching a single
                    line of code.</p>
            </div>

            <div class="simple-feature">
                <div class="simple-icon" style="color:#10B981; background:rgba(16,185,129,0.1);"><i
                        class="fa-solid fa-bolt"></i></div>
                <h3>Lightning Fast</h3>
                <p>Global edge caching ensures your store loads in milliseconds anywhere in the world.</p>
            </div>

            <div class="simple-feature">
                <div class="simple-icon" style="color:#F59E0B; background:rgba(245,158,11,0.1);"><i
                        class="fa-solid fa-mobile-screen"></i></div>
                <h3>Mobile Native Apps</h3>
                <p>Generate native iOS and Android apps directly from your storefront configuration automatically.</p>
            </div>

            <div class="simple-feature">
                <div class="simple-icon" style="color:#EC4899; background:rgba(236,72,153,0.1);"><i
                        class="fa-solid fa-chart-pie"></i></div>
                <h3>Advanced Analytics</h3>
                <p>Track customer journeys, monitor cart abandonment, and optimize your pricing strategy with real-time
                    insights.</p>
            </div>

            <div class="simple-feature">
                <div class="simple-icon" style="color:#3B82F6; background:rgba(59,130,246,0.1);"><i
                        class="fa-solid fa-credit-card"></i></div>
                <h3>Global Payments</h3>
                <p>Accept cards, Apple Pay, and crypto seamlessly with our secure integrated payment gateways.</p>
            </div>

            <div class="simple-feature">
                <div class="simple-icon" style="color:#8B5CF6; background:rgba(139,92,246,0.1);"><i
                        class="fa-solid fa-boxes-stacked"></i></div>
                <h3>Inventory Management</h3>
                <p>Track stock levels, set up automated reorders, and manage multiple warehouses from a single dashboard.
                </p>
            </div>
        </div>
    </section>

    <!-- ================= PRICING ================= -->
    <section class="pricing-v2" id="pricing">
        <div class="section-title">
            <h2>Simple, transparent pricing</h2>
            <p>No hidden fees. Choose the plan that best fits your growing business.</p>
        </div>

        <div class="pricing-grid">
            <!-- Base Plan -->
            <div class="pricing-card">
                <div class="plan-name">Base Plan</div>
                <div class="plan-price">₹99<span>/month</span></div>
                <p class="plan-desc">Everything you need to get your store up and running quickly.</p>
                <ul class="plan-features">
                    <li><i class="fa-solid fa-check-circle"></i> Online Store</li>
                    <li><i class="fa-solid fa-check-circle"></i> Unlimited visitors</li>
                    <li><i class="fa-solid fa-check-circle"></i> Unlimited orders</li>
                    <li><i class="fa-solid fa-check-circle"></i> Sell Unlimited products</li>
                    <li><i class="fa-solid fa-check-circle"></i> Free MetoHub Subdomain</li>
                    <li><i class="fa-solid fa-check-circle"></i> Chat and call support</li>
                </ul>
                <a href="#" class="plan-btn plan-btn-outline">Start 7-Day Free Trial</a>
            </div>

            <!-- Pro Plan -->
            <div class="pricing-card popular">
                <div class="popular-badge">Most Popular</div>
                <div class="plan-name">Pro Plan</div>
                <div class="plan-price">₹249<span>/month</span></div>
                <p class="plan-desc">For growing businesses that need advanced features and scale.</p>
                <ul class="plan-features">
                    <li><i class="fa-solid fa-check-circle" style="color: var(--v2-primary);"></i> <strong>Everything in Base, plus:</strong></li>
                    <li><i class="fa-solid fa-check-circle"></i> Custom Domain Support</li>
                    <li><i class="fa-solid fa-check-circle"></i> Advanced Analytics & Reports</li>
                    <li><i class="fa-solid fa-check-circle"></i> Abandoned Cart Recovery</li>
                    <li><i class="fa-solid fa-check-circle"></i> No watermark in footer</li>
                </ul>
                <a href="#" class="plan-btn plan-btn-primary">Start 7-Day Free Trial</a>
            </div>

            <!-- Advanced Plan -->
            <div class="pricing-card">
                <div class="plan-name">Enterprise</div>
                <div class="plan-price">₹749<span>/month</span></div>
                <p class="plan-desc">Maximum performance and dedicated support for large scale operations.</p>
                <ul class="plan-features">
                    <li><i class="fa-solid fa-check-circle" style="color: var(--v2-primary);"></i> <strong>Everything in Pro, plus:</strong></li>
                    <li><i class="fa-solid fa-check-circle"></i> Multi-Warehouse Inventory</li>
                    <li><i class="fa-solid fa-check-circle"></i> Custom Integrations (API)</li>
                    <li><i class="fa-solid fa-check-circle"></i> Dedicated Account Manager</li>
                </ul>
                <a href="#" class="plan-btn plan-btn-outline">Start 7-Day Free Trial</a>
            </div>
        </div>
    </section>

    <!-- ================= CTA ================= -->
    <section style="padding: 100px 5%; background: var(--v2-dark); color: white; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="color: white; font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 24px;">Ready to start your eCommerce
                journey?</h2>
            <p style="font-size: 1.2rem; color: #94A3B8; margin-bottom: 40px;">Join thousands of merchants building the
                future of retail on MetoHub.</p>
            <a href="#" class="v2-btn v2-btn-primary" style="font-size: 1.1rem; padding: 18px 40px;">Create Your Store
                Today</a>
        </div>
    </section>
@endsection