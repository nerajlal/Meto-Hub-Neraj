@extends('template_3.layouts.app')

@section('title', 'Our Story | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
    <div class="about-hero"
        style="height: 50vh; min-height: 400px; background: linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&q=80&w=1600'); background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; text-align: center; border-radius: 0 0 2rem 2rem; margin-top: -2rem; padding: 0 1.5rem;">
        <div class="about-hero-content" style="max-width: 800px; color: #fff;">
            <span class="eyebrow"
                style="text-transform: uppercase; letter-spacing: 0.3em; font-weight: 700; color: var(--accent-color); font-size: 0.85rem; display: block; margin-bottom: 1.25rem;">Farm
                to Your Doorstep</span>
            <h1 class="about-title" style="font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem;">
                Cultivating Health & happiness</h1>
            <p class="about-subtitle" style="font-size: 1.15rem; opacity: 0.9; line-height: 1.6;">Bringing you the freshest,
                locally sourced organic produce and high-quality daily essentials. We believe in food that is good for you
                and good for the planet.</p>
        </div>
    </div>

    <div class="about-container" style="max-width: 1200px; margin: 0 auto; padding: 4rem 1.5rem;">
        <!-- Our Philosophy Section -->
        <section class="about-section philosophy" style="margin-bottom: 6rem;">
            <div class="section-grid"
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem; align-items: center;">
                <div class="section-image">
                    <img src="https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?auto=format&fit=crop&q=80&w=800"
                        alt="Fresh Harvest" style="width: 100%; border-radius: 2rem; box-shadow: var(--shadow-md);">
                </div>
                <div class="section-text">
                    <span class="section-badge"
                        style="display: inline-block; padding: 0.4rem 1rem; background: var(--section-bg); color: var(--primary-color); border-radius: 99px; font-weight: 700; font-size: 0.8rem; margin-bottom: 1.25rem;">Our
                        Philosophy</span>
                    <h2 class="h1"
                        style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.25rem;">
                        {{ $currentTenant->about_title ?? 'Pure, Pesticide-Free, Fresh' }}</h2>
                    @if($currentTenant->about_text)
                        <div style="font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1.25rem;">
                            {!! nl2br(e($currentTenant->about_text)) !!}
                        </div>
                    @else
                        <p style="font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1.25rem;">
                            {{ $currentTenant->name ?? 'Fresh Grocery' }} was founded on a simple principle: everyone deserves
                            access to healthy, clean food. We partner directly with local, certified organic farmers to cut out
                            the middlemen and deliver produce within hours of harvest.</p>
                        <p style="font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1.25rem;">Every
                            item is picked at peak ripeness, checked thoroughly for quality, and packaged hygienically to
                            preserve nutritional values and natural taste.</p>
                    @endif
                    <div class="stat-grid"
                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 2rem;">
                        <div class="stat-item">
                            <span class="stat-num"
                                style="display: block; font-size: 2rem; font-weight: 800; color: var(--accent-color);">100%</span>
                            <span class="stat-label"
                                style="font-size: 0.9rem; font-weight: 700; color: var(--primary-color);">Organic
                                Certified</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-num"
                                style="display: block; font-size: 2rem; font-weight: 800; color: var(--accent-color);">2
                                Hours</span>
                            <span class="stat-label"
                                style="font-size: 0.9rem; font-weight: 700; color: var(--primary-color);">Express
                                Delivery</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- The Process Section -->
        <!-- <section class="about-section process inverted" style="margin-bottom: 6rem;">
            <div class="section-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 4rem; align-items: center;">
                <div class="section-text" style="order: 2;">
                    <span class="section-badge" style="display: inline-block; padding: 0.4rem 1rem; background: var(--section-bg); color: var(--primary-color); border-radius: 99px; font-weight: 700; font-size: 0.8rem; margin-bottom: 1.25rem;">Quality Standard</span>
                    <h2 class="h1" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.25rem;">Meticulous Farm-To-Table Quality</h2>
                    <p style="font-size: 1.1rem; color: var(--text-muted); line-height: 1.7; margin-bottom: 1.25rem;">We hold ourselves to the highest standards of safety and freshness. Our logistics network maintains cold chain integrity from farms to our warehouse, so greens remain crisp and fresh.</p>
                    <ul class="check-list" style="list-style: none; padding: 0; margin-top: 1.5rem;">
                        <li style="margin-bottom: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; color: var(--primary-color); font-size: 1rem;"><i class="fa-solid fa-check" style="color: #10b981;"></i> Multi-stage hygiene sorting process.</li>
                        <li style="margin-bottom: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; color: var(--primary-color); font-size: 1rem;"><i class="fa-solid fa-check" style="color: #10b981;"></i> Eco-friendly, food-grade paper packaging.</li>
                        <li style="margin-bottom: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.75rem; color: var(--primary-color); font-size: 1rem;"><i class="fa-solid fa-check" style="color: #10b981;"></i> Zero chemical preservatives or synthetic ripening agents.</li>
                    </ul>
                </div>
                <div class="section-image" style="order: 1;">
                    <img src="https://images.unsplash.com/photo-1595974482597-4b8da8879bc5?auto=format&fit=crop&q=80&w=800" alt="Fresh Greens" style="width: 100%; border-radius: 2rem; box-shadow: var(--shadow-md);">
                </div>
            </div>
        </section> -->

        <!-- Values / USP Section -->
        <section class="values-grid"
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin: 6rem 0;">
            <div class="value-card"
                style="background: #fff; padding: 3rem 2rem; border-radius: 2rem; border: 1px solid var(--border-color); text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-seedling"
                    style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1.5rem; display: block;"></i>
                <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.75rem;">
                    Directly From Farms</h3>
                <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.95rem;">By cutting out intermediate
                    stores, we guarantee that all veggies and fruits reach you fresher and last longer.</p>
            </div>
            <div class="value-card"
                style="background: #fff; padding: 3rem 2rem; border-radius: 2rem; border: 1px solid var(--border-color); text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-shield-heart"
                    style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1.5rem; display: block;"></i>
                <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.75rem;">100%
                    Quality Guarantee</h3>
                <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.95rem;">Not satisfied with the quality of
                    any fresh item? We offer an instant, no-questions-asked refund at delivery.</p>
            </div>
            <div class="value-card"
                style="background: #fff; padding: 3rem 2rem; border-radius: 2rem; border: 1px solid var(--border-color); text-align: center; transition: 0.3s;">
                <i class="fa-solid fa-leaf"
                    style="font-size: 2.5rem; color: var(--accent-color); margin-bottom: 1.5rem; display: block;"></i>
                <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--primary-color); margin-bottom: 0.75rem;">
                    Eco-Conscious Choice</h3>
                <p style="color: var(--text-muted); line-height: 1.5; font-size: 0.95rem;">All fresh deliveries are shipped
                    in compostable cardboard baskets and reusable cloth grocery bags.</p>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="about-cta"
            style="background: var(--primary-color); color: #fff; padding: 4rem 2rem; border-radius: 3rem; text-align: center;">
            <div class="cta-inner">
                <h2 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 1.25rem;">Experience Freshness Today</h2>
                <p style="font-size: 1.1rem; opacity: 0.8; margin-bottom: 2rem;">Ready to try farm-fresh products? Explore
                    our collections today.</p>
                <div style="display: flex; gap: 1.5rem; justify-content: center; margin-top: 2rem; flex-wrap: wrap;">
                    <a href="{{ route('v3.all-products') }}" class="btn-primary"
                        style="background: var(--accent-color); color: #fff; padding: 0.9rem 2.5rem; border-radius: 99px; text-decoration: none; font-weight: 700; display: inline-block;">Shop
                        All Groceries</a>
                    <a href="{{ route('v3.combos') }}" class="btn-outline"
                        style="display: inline-block; padding: 0.9rem 2.5rem; border-radius: 99px; border: 2px solid rgba(255,255,255,0.3); color: #fff; text-decoration: none; font-weight: 700; transition: 0.3s;">Weekly
                        Combos</a>
                </div>
            </div>
        </section>
    </div>
@endsection