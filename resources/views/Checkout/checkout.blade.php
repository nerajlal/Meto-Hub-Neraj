@extends($layout ?? 'template_1.layouts.app')

@section('title', 'Secure Checkout | ' . ($currentTenant->name ?? 'Fresh Grocery'))

@section('content')
<style>
    .checkout-main-grid {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 3rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .checkout-main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<div class="checkout-page-container" style="max-width: 1200px; margin: 0 auto; padding: 2rem 1rem;">
    <div class="checkout-header-lg" style="margin-bottom: 2.5rem;">
        <h1 class="checkout-title-lg" style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color); margin-bottom: 0.5rem;">Secure Checkout</h1>
        <p class="checkout-subtitle" style="color: var(--text-muted); font-size: 1.05rem;">Review your fresh items and complete your order.</p>
    </div>

    <div class="checkout-main-grid">
        <!-- Checkout forms -->
        <div class="checkout-forms-panel">
            <form action="{{ route('order.place') }}" method="POST" id="main-checkout-form">
                @csrf
                
                <!-- Contact Details -->
                <div class="checkout-card" style="background: #fff; border: 1px solid var(--border-color); border-radius: 1.5rem; padding: 2.5rem; margin-bottom: 2rem;">
                    <h2 class="card-heading" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <i class="fa-solid fa-circle-user" style="color: var(--accent-color);"></i> 1. Contact Information
                    </h2>
                    <div class="form-grid-lg" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group-lg full" style="grid-column: span 2;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Full Name</label>
                            <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" required placeholder="e.g. John Doe" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group-lg" style="grid-column: span 2;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Email Address</label>
                            <input type="email" name="email" value="{{ auth()->user()->email ?? '' }}" required placeholder="e.g. john@example.com" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group-lg" style="grid-column: span 2;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Phone Number</label>
                            <input type="tel" name="phone" value="{{ auth()->user()->phone ?? '' }}" required placeholder="e.g. 9876543210" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="checkout-card" style="background: #fff; border: 1px solid var(--border-color); border-radius: 1.5rem; padding: 2.5rem; margin-bottom: 2rem;">
                    <h2 class="card-heading" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <span><i class="fa-solid fa-truck-fast" style="color: var(--accent-color);"></i> 2. Delivery Address</span>
                        <button type="button" id="btn-geolocation" onclick="detectLocation()" style="background: #f1f5f9; border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 0.75rem; font-weight: 700; font-size: 0.8rem; cursor: pointer; color: var(--primary-color); display: flex; align-items: center; gap: 0.5rem; transition: 0.2s;">
                            <i class="fa-solid fa-location-crosshairs" style="color: var(--accent-color);"></i> Use Current Location
                        </button>
                    </h2>
                    <div class="form-grid-lg" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group-lg full" style="grid-column: span 2;">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Street Address</label>
                            <input type="text" name="address" value="{{ $address->address ?? '' }}" required placeholder="House No, Apartment, Street Name" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group-lg">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">City</label>
                            <input type="text" name="city" value="{{ $address->city ?? '' }}" required placeholder="City" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group-lg">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">State</label>
                            <input type="text" name="state" value="{{ $address->state ?? '' }}" required placeholder="State" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group-lg">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">PIN Code</label>
                            <input type="text" name="pincode" value="{{ $address->pincode ?? '' }}" required placeholder="6-digit PIN" style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; outline: none;">
                        </div>
                        <div class="form-group-lg">
                            <label style="display: block; font-size: 0.8rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Country</label>
                            <input type="text" value="India" disabled style="width: 100%; padding: 0.85rem 1.25rem; border: 2px solid var(--border-color); border-radius: 0.75rem; font-size: 1rem; background: var(--section-bg); cursor: not-allowed; outline: none;">
                        </div>
                    </div>
                </div>

                <!-- Payment Selection -->
                <div class="checkout-card" style="background: #fff; border: 1px solid var(--border-color); border-radius: 1.5rem; padding: 2.5rem; margin-bottom: 2rem;">
                    <h2 class="card-heading" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; color: var(--primary-color); border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <i class="fa-solid fa-credit-card" style="color: var(--accent-color);"></i> 3. Payment Method
                    </h2>
                    <div class="payment-selection-grid" style="display: grid; grid-template-columns: 1fr; gap: 1rem;">
                        <label class="pay-option active" style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; border: 2px solid var(--accent-color); border-radius: 1rem; cursor: pointer; background: var(--section-bg); transition: 0.2s;">
                            <input type="radio" name="payment_method" value="cod" checked style="accent-color: var(--accent-color); transform: scale(1.25);">
                            <div class="pay-content" style="flex-grow: 1;">
                                <span class="pay-title" style="display: block; font-weight: 700; color: var(--primary-color); font-size: 1.1rem; margin-bottom: 0.25rem;">Cash on Delivery (COD)</span>
                                <span class="pay-desc" style="font-size: 0.85rem; color: var(--text-muted);">Pay with cash or UPI at your doorstep upon delivery.</span>
                            </div>
                            <i class="fa-solid fa-money-bill-1-wave" style="font-size: 1.5rem; color: var(--accent-color);"></i>
                        </label>
                        <label class="pay-option" style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; border: 2px solid var(--border-color); border-radius: 1rem; cursor: pointer; transition: 0.2s;">
                            <input type="radio" name="payment_method" value="online" style="accent-color: var(--accent-color); transform: scale(1.25);">
                            <div class="pay-content" style="flex-grow: 1;">
                                <span class="pay-title" style="display: block; font-weight: 700; color: var(--primary-color); font-size: 1.1rem; margin-bottom: 0.25rem;">Pay Online Securely</span>
                                <span class="pay-desc" style="font-size: 0.85rem; color: var(--text-muted);">Pay instantly via Card, NetBanking, or UPI apps.</span>
                            </div>
                            <i class="fa-solid fa-shield-check" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-complete-order" style="width: 100%; background: var(--accent-color); color: #fff; border: none; padding: 1.25rem; border-radius: 9999px; font-weight: 800; font-size: 1.2rem; cursor: pointer; transition: 0.2s;">
                    Complete Order • ₹{{ number_format($total, 2) }}
                </button>
            </form>
        </div>

        <!-- Order Summary panel -->
        <aside class="checkout-sticky-summary" style="position: sticky; top: 100px;">
            <div class="order-summary-card" style="background: #f8fafc; padding: 2.5rem; border-radius: 1.5rem; border: 1px solid var(--border-color);">
                <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.5rem;">Your Order Basket</h3>
                <div class="summary-items-scroll" style="max-height: 280px; overflow-y: auto; margin-bottom: 1.5rem; padding-right: 0.5rem;">
                    @foreach($cart as $item)
                    <div class="s-item-row" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1.25rem;">
                        <div class="s-item-img" style="width: 60px; height: 60px; border-radius: 0.75rem; overflow: hidden; background: #fff; border: 1px solid var(--border-color); position: relative; flex-shrink: 0;">
                            @php 
                                $checkoutImg = $item['image'] ?? asset('Images/placeholder-grocery.webp');
                            @endphp
                            <img src="{{ $checkoutImg }}" alt="{{ $item['name'] }}" onerror="this.src='{{ asset('Images/placeholder-grocery.webp') }}'" style="width: 100%; height: 100%; object-fit: cover;">
                            <span class="s-item-qty" style="position: absolute; top: -5px; right: -5px; background: var(--primary-color); color: #fff; width: 20px; height: 20px; border-radius: 50%; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 2px solid #fff;">{{ $item['quantity'] }}</span>
                        </div>
                        <div style="flex-grow: 1; min-width: 0;">
                            <span style="display: block; font-size: 0.95rem; font-weight: 700; color: var(--primary-color); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item['name'] }}</span>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ $item['size'] }}</span>
                        </div>
                        <span style="font-weight: 700; color: var(--primary-color); font-size: 0.95rem;">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                    </div>
                    @endforeach
                </div>

                <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: var(--text-muted);">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($subtotal, 2) }}</span>
                    </div>
                    @if($savings > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: #10b981; font-weight: 600;">
                        <span>Volume Discount</span>
                        <span>-₹{{ number_format($savings, 2) }}</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: var(--text-muted);">
                        <span>Shipping</span>
                        <span style="color: #10b981; font-weight: 700;">FREE</span>
                    </div>
                    @if(isset($taxName) && isset($taxRate) && $taxAmount > 0)
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; font-size: 0.95rem; color: var(--text-muted);">
                        <span>{{ $taxName }} ({{ $taxRate }}%)</span>
                        <span>₹{{ number_format($taxAmount, 2) }}</span>
                    </div>
                    @endif
                    <div style="border-top: 2px solid var(--border-color); padding-top: 1rem; display: flex; justify-content: space-between; font-size: 1.4rem; font-weight: 800; color: var(--primary-color);">
                        <span>Grand Total</span>
                        <span>₹{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="summary-footer-badges" style="display: flex; justify-content: space-between; gap: 0.5rem; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                    <div class="badge-item" style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 0.25rem;"><i class="fa-solid fa-lock" style="font-size: 1.1rem; color: var(--accent-color);"></i> SSL Secure</div>
                    <div class="badge-item" style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 0.25rem;"><i class="fa-solid fa-circle-check" style="font-size: 1.1rem; color: var(--accent-color);"></i> Certified</div>
                    <div class="badge-item" style="font-size: 0.65rem; font-weight: 700; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 0.25rem;"><i class="fa-solid fa-house-chimney-user" style="font-size: 1.1rem; color: var(--accent-color);"></i> Fresh Delivery</div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function detectLocation() {
        const btn = document.getElementById('btn-geolocation');
        const originalHtml = btn.innerHTML;

        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Getting Location...';
        btn.disabled = true;

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                $.ajax({
                    url: `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`,
                    method: 'GET',
                    headers: {
                        'Accept-Language': 'en'
                    },
                    success: function(data) {
                        if (data && data.address) {
                            const addr = data.address;
                            const street = addr.road || addr.suburb || addr.neighbourhood || addr.city_district || '';
                            const houseNumber = addr.house_number || '';
                            const fullStreet = (houseNumber + ' ' + street).trim() || data.display_name;
                            const city = addr.city || addr.town || addr.village || addr.municipality || '';
                            const state = addr.state || addr.province || '';
                            const pincode = addr.postcode || '';

                            document.getElementsByName('address')[0].value = fullStreet;
                            document.getElementsByName('city')[0].value = city;
                            document.getElementsByName('state')[0].value = state;
                            document.getElementsByName('pincode')[0].value = pincode;
                        } else {
                            alert('Could not resolve coordinates to address.');
                        }
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    },
                    error: function() {
                        alert('Could not connect to location lookup service.');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                });
            },
            function(error) {
                let msg = 'Failed to retrieve location.';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Permission denied. Please grant location access in your browser settings.';
                }
                alert(msg);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    $(document).ready(function() {
        $('#main-checkout-form').on('submit', function(e) {
            e.preventDefault();
            
            const $btn = $('.btn-complete-order');
            const originalHtml = $btn.html();
            
            $btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Processing Order...').prop('disabled', true);
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert(response.message || 'Something went wrong.');
                        $btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    let msg = 'Something went wrong. Please try again.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    alert(msg);
                    $btn.html(originalHtml).prop('disabled', false);
                }
            });
        });

        // Payment option toggle
        $('.pay-option').on('click', function() {
            $('.pay-option').removeClass('active').css('border-color', 'var(--border-color)').css('background', 'none');
            $(this).addClass('active').css('border-color', 'var(--accent-color)').css('background', 'var(--section-bg)');
            $(this).find('input[type="radio"]').prop('checked', true);
        });
    });
</script>
@endsection
