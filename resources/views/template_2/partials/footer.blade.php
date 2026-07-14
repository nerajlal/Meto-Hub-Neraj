<footer class="store-footer">
    <div class="footer-content-wrapper">
        <div class="footer-container">
            <div class="footer-brand">
                <a href="{{ route('velvet.home') }}" class="logo" style="color: var(--accent-color); font-weight: 800; font-size: 1.5rem; text-decoration: none; display: flex; align-items: center;">
                    @if($currentTenant->logo)
                        <img src="{{ Storage::url($currentTenant->logo) }}" alt="{{ $currentTenant->name }}" style="max-height: 40px; width: auto; object-fit: contain;">
                    @else
                        <i class="fa-solid fa-leaf me-2"></i>{{ $currentTenant->name ?? 'Fresh Grocery' }}
                    @endif
                </a>
                <p>Bringing fresh, high-quality organic vegetables, fruits, dairy, and daily essentials straight from local farms to your kitchen. Fast delivery guaranteed.</p>
                <div class="footer-trust">
                    <div class="payment-methods">
                        <i class="fa-brands fa-cc-visa"></i>
                        <i class="fa-brands fa-cc-mastercard"></i>
                        <i class="fa-brands fa-cc-apple-pay"></i>
                        <i class="fa-brands fa-google-pay"></i>
                    </div>
                    <div class="secure-badge">
                        <i class="fa-solid fa-shield-check"></i> 100% Safe Payments
                    </div>
                </div>
            </div>

            <div class="footer-group">
                <h3 class="footer-heading">Shop Categories</h3>
                <ul class="footer-links">
                    @php $footerCols = \App\Models\Collection::where('tenant_id', $currentTenant->id ?? 2)->where('status', 1)->take(5)->get(); @endphp
                    @foreach($footerCols as $col)
                        <li><a href="{{ route('velvet.collection', ['slug' => $col->slug]) }}">{{ $col->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="footer-group">
                <h3 class="footer-heading">Customer Care</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('velvet.about') }}">About Us</a></li>
                    <li><a href="{{ route('velvet.contact') }}">Contact Support</a></li>
                    <li><a href="{{ route('velvet.shipping-policy') }}">Shipping Policy</a></li>
                    <li><a href="{{ route('velvet.return-policy') }}">Return Policy</a></li>
                </ul>
            </div>

            <div class="footer-newsletter">
                <h3 class="footer-heading">Weekly Savings</h3>
                <p>Subscribe to our newsletter to receive weekly discount coupons and farm fresh updates.</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Enter your email">
                    <button type="button" style="background-color: var(--accent-color);">Join</button>
                </form>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                &copy; {{ date('Y') }} {{ $currentTenant->name ?? 'Fresh Grocery' }}. All rights reserved. <span style="opacity: 0.7; margin-left: 10px;">| Powered by <a href="#" style="color: inherit; text-decoration: none; font-weight: 600;">Grocery SaaS</a></span>
            </div>
            <div class="social-links">
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                <a href="#"><i class="fa-brands fa-pinterest"></i></a>
            </div>
        </div>
    </div>
</footer>
