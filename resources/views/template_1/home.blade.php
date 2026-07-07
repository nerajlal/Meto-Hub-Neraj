@extends('template_1.layouts.app')

@section('title', ($currentTenant->name ?? 'Fresh Grocery') . ' | Farm Fresh Groceries & Daily Essentials')
@section('meta_description', 'Order farm fresh vegetables, organic fruits, dairy products, bakery goods, and daily essentials online. Super-fast home delivery guaranteed.')
@section('meta_keywords', 'online grocery store, fresh vegetables, buy organic fruits, dairy delivery, daily essentials shop')

@section('content')
    <!-- Hero Banner Section -->
    <style>
        .hero-slider::-webkit-scrollbar {
            display: none;
        }
        .hero-slider {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .slider-dot.active {
            background: #fff !important;
            width: 20px !important;
            border-radius: 5px !important;
        }
    </style>
    <div class="hero-slider-container" style="margin-bottom: 2rem; border-radius: 2rem; overflow: hidden; position: relative;">
        <div class="hero-slider" style="display: flex; overflow-x: auto; scroll-snap-type: x mandatory; scroll-behavior: smooth; -webkit-overflow-scrolling: touch; border-radius: 2rem;">
            @forelse($sliders as $slider)
                <div class="hero-slide" style="flex: 0 0 100%; width: 100%; scroll-snap-align: start; position: relative; line-height: 0;">
                    <picture style="width: 100%; display: block;">
                        @php
                            $mobileUrl = Str::startsWith($slider->image_mobile, 'http') ? $slider->image_mobile : (Str::startsWith($slider->image_mobile, 'Images/') ? asset($slider->image_mobile) : \Illuminate\Support\Facades\Storage::url($slider->image_mobile));
                            $desktopUrl = Str::startsWith($slider->image_desktop, 'http') ? $slider->image_desktop : (Str::startsWith($slider->image_desktop, 'Images/') ? asset($slider->image_desktop) : \Illuminate\Support\Facades\Storage::url($slider->image_desktop));
                        @endphp
                        <source media="(max-width: 768px)" srcset="{{ $mobileUrl }}">
                        <img src="{{ $desktopUrl }}" alt="{{ $slider->title ?? 'Banner' }}" style="width: 100%; height: auto; border-radius: 2rem; object-fit: cover;">
                    </picture>
                </div>
            @empty
                <div class="hero-slide" style="flex: 0 0 100%; width: 100%; scroll-snap-align: start; position: relative; line-height: 0;">
                    <picture style="width: 100%; display: block;">
                        <source media="(max-width: 768px)" srcset="{{ asset('Images/placeholder-grocery.webp') }}">
                        <img src="{{ asset('Images/placeholder-grocery.webp') }}" alt="Default Banner" style="width: 100%; height: auto; border-radius: 2rem; object-fit: cover;">
                    </picture>
                </div>
            @endforelse
        </div>
        @if(isset($sliders) && $sliders->count() > 1)
            <div class="slider-dots" style="position: absolute; bottom: 15px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
                @foreach($sliders as $index => $slider)
                    <span class="slider-dot {{ $loop->first ? 'active' : '' }}" onclick="scrollToSlide({{ $index }})" style="width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.5); cursor: pointer; transition: 0.3s;"></span>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        let currentSlide = 0;
        const slidesCount = {{ isset($sliders) ? $sliders->count() : 0 }};
        
        function scrollToSlide(index) {
            const slider = document.querySelector('.hero-slider');
            if (slider) {
                const slideWidth = slider.clientWidth;
                slider.scrollTo({
                    left: slideWidth * index,
                    behavior: 'smooth'
                });
                currentSlide = index;
                updateDots(index);
            }
        }
        
        function updateDots(index) {
            document.querySelectorAll('.slider-dot').forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        if (slidesCount > 1) {
            setInterval(() => {
                currentSlide = (currentSlide + 1) % slidesCount;
                scrollToSlide(currentSlide);
            }, 5000);
        }
    </script>
    
    <!-- USP Trust Bar -->
    <div class="usp-bar" style="background: #fff; border: 1px solid var(--border-color); border-radius: 1.5rem; padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 3rem; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <div class="usp-item" style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #ecfdf5; color: var(--accent-color); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-carrot"></i>
            </div>
            <div class="usp-text">
                <span class="usp-title" style="display: block; font-weight: 700; font-size: 0.95rem;">100% Farm Fresh</span>
                <span class="usp-desc" style="display: block; font-size: 0.75rem; color: var(--text-muted);">Sourced directly from local farms</span>
            </div>
        </div>
        <div class="usp-item" style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #ecfdf5; color: var(--accent-color); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div class="usp-text">
                <span class="usp-title" style="display: block; font-weight: 700; font-size: 0.95rem;">Delivered in {{ $currentTenant->delivery_days ?? 2 }} {{ ($currentTenant->delivery_days ?? 2) == 1 ? 'day' : 'days' }}</span>
                <span class="usp-desc" style="display: block; font-size: 0.75rem; color: var(--text-muted);">Fast delivery to your doorstep from us</span>
            </div>
        </div>
        <div class="usp-item" style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #ecfdf5; color: var(--accent-color); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="usp-text">
                <span class="usp-title" style="display: block; font-weight: 700; font-size: 0.95rem;">Hygienically Packed</span>
                <span class="usp-desc" style="display: block; font-size: 0.75rem; color: var(--text-muted);">Handled with strict safety protocols</span>
            </div>
        </div>
        <div class="usp-item" style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: #ecfdf5; color: var(--accent-color); width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-rotate"></i>
            </div>
            <div class="usp-text">
                <span class="usp-title" style="display: block; font-weight: 700; font-size: 0.95rem;">No Questions Return</span>
                <span class="usp-desc" style="display: block; font-size: 0.75rem; color: var(--text-muted);">Instant returns at delivery window</span>
            </div>
        </div>
    </div>

    <!-- Shop by Category / Collections Row -->
    @if(isset($collections) && $collections->count() > 0)
    <div class="collections-horizontal-scroll" style="margin-bottom: 2rem; padding: 0 1rem;">
        <h3 style="font-size: 1.1rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1rem;">Shop by Category</h3>
        <div style="display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 0.5rem; scrollbar-width: none; -ms-overflow-style: none;" class="hide-scroll">
            <style>
                .hide-scroll::-webkit-scrollbar { display: none; }
            </style>
            @foreach($collections as $c)
            <a href="{{ route('v3.collection', ['slug' => $c->slug]) }}" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; text-decoration: none; min-width: 75px;">
                <div style="width: 70px; height: 70px; border-radius: 50%; background: #f8fafc; border: 1.5px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; padding: 0.2rem; transition: border-color 0.2s;" onmouseover="this.style.borderColor='var(--accent-color)'" onmouseout="this.style.borderColor='var(--border-color)'">
                    @php 
                        $catImage = $c->image ? Storage::url($c->image) : asset('Images/placeholder-grocery.webp');
                    @endphp
                    <img src="{{ $catImage }}" alt="{{ $c->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                </div>
                <span style="font-size: 0.75rem; font-weight: 700; color: var(--primary-color); text-align: center; line-height: 1.2;">{{ $c->name }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Collections Sections (Department Style) -->
    @php 
        $collections = \App\Models\Collection::where('tenant_id', $currentTenant->id ?? 2)->with(['products' => function($query) {
            $query->where('status', 'active')->take(8);
        }])->where('status', 1)->get(); 
        $hasProducts = $collections->contains(function($c) {
            return $c->products->count() > 0;
        });
    @endphp

    @if($collections->count() > 0 && $hasProducts)
        @foreach($collections as $collection)
            @if($collection->products->count() > 0)
            <div class="department-section" id="collection-{{ $collection->id }}" style="margin-bottom: 3rem;">
                <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color); position: relative;">{{ $collection->name }}</h2>
                    <a href="{{ route('v3.collection', ['slug' => $collection->slug]) }}" class="view-all" style="color: var(--accent-color); text-decoration: none; font-weight: 700; font-size: 0.9rem;">View All <i class="fa-solid fa-chevron-right ms-1" style="font-size: 0.75rem;"></i></a>
                </div>
                
                <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
                    @foreach($collection->products as $product)
                        @include('template_1.partials.product_card', ['product' => $product])
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    @else
        <!-- Fallback mock products if seeder has not run yet -->
        <div class="department-section" style="margin-bottom: 3rem;">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">Fresh Vegetables</h2>
                <a href="javascript:void(0)" class="view-all" style="color: var(--accent-color); text-decoration: none; font-weight: 700;">View All <i class="fa-solid fa-chevron-right ms-1"></i></a>
            </div>
            
            <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
                @foreach(['Fresh Tomatoes', 'Organic Bananas', 'Whole Wheat Bread', 'Organic Milk'] as $fallbackName)
                <div class="product-card" style="border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; background: #fff; padding: 1rem; text-align: center; position: relative;">
                    <div style="background: #f8fafc; height: 160px; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <i class="fa-solid fa-basket-shopping fa-3x text-muted opacity-20"></i>
                    </div>
                    <span style="font-weight: 800; font-size: 1.15rem; color: var(--accent-color); display: block;">₹99.00</span>
                    <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0.5rem 0;">{{ $fallbackName }}</h3>
                    <button class="cart-add-btn" style="position: absolute; bottom: 10px; right: 10px; width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- Weekly Combos & Bundles -->
    @if(isset($bundles) && $bundles->count() > 0)
    <div class="department-section" style="margin-bottom: 3rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);">Weekly Grocery Combos</h2>
            <a href="{{ route('v3.combos') }}" class="view-all" style="color: var(--accent-color); text-decoration: none; font-weight: 700; font-size: 0.9rem;">View All <i class="fa-solid fa-chevron-right ms-1"></i></a>
        </div>
        
        <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
            @forelse($bundles as $bundle)
                <div class="product-card" style="border: 1px solid var(--border-color); border-radius: 1rem; overflow: hidden; background: #fff; position: relative;">
                    <a href="{{ route('v3.combo', ['id' => $bundle->id]) }}" class="card-img" style="display: block; position: relative; padding-top: 100%; background: #f8fafc;">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($bundle->image) }}" alt="{{ $bundle->title }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; top: 10px; left: 10px; background: #10b981; color: #fff; padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap;">
                            <i class="fa-solid fa-layer-group me-1"></i><span class="hide-text-mobile">Save Bundle</span>
                        </div>
                    </a>
                    <div class="card-info" style="padding: 1rem; display: flex; flex-direction: column; gap: 0.25rem;">
                        <span class="p-price" style="font-weight: 800; font-size: 1.15rem; color: var(--accent-color);">₹{{ number_format($bundle->total_price, 2) }}</span>
                        <a href="{{ route('v3.combo', ['id' => $bundle->id]) }}" class="p-name" style="font-weight: 700; font-size: 0.95rem; color: var(--primary-color); text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6rem;">{{ $bundle->title }}</a>
                        <span class="p-meta" style="font-size: 0.75rem; color: var(--text-muted);">{{ $bundle->products->count() }} Products Included</span>
                    </div>
                    <button class="cart-add-btn" data-product-id="{{ $bundle->id }}" data-type="bundle" style="position: absolute; bottom: 10px; right: 10px; width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; border: none; display: flex; align-items: center; justify-content: center; color: var(--primary-color); cursor: pointer;">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            @empty
                <!-- No Bundles -->
            @endforelse
        </div>
    </div>
    @endif

    <!-- Newsletter Section -->
    <style>
        .newsletter-input::placeholder {
            color: #94a3b8 !important;
        }
    </style>
    <div class="newsletter-section" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; padding: 4rem 2rem; border-radius: 2rem; text-align: center; margin-bottom: 2rem;">
        <div class="newsletter-content" style="max-width: 650px; margin: 0 auto;">
            <h2 class="newsletter-title" style="font-size: 2.2rem; font-weight: 800; margin-bottom: 1rem; font-family: 'Outfit', sans-serif;">Subscribe for Special Offers</h2>
            <p class="newsletter-subtitle" style="font-size: 1rem; color: #94a3b8; margin-bottom: 2rem; line-height: 1.5;">Get updates on new seasonal arrivals, farm harvest schedules, weekly coupons, and grocery discounts straight to your inbox.</p>
            <form class="newsletter-input-group">
                <input type="email" placeholder="Your email address" class="newsletter-input" style="color: var(--primary-color); outline: none;">
                <button type="button" class="newsletter-btn" style="background-color: var(--accent-color); color: #fff;">Subscribe</button>
            </form>
        </div>
    </div>
@endsection
