@extends('layouts.admin')

@section('title', 'Storefront Pages Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Storefront Pages Settings</h1>
            <p class="text-muted">Manage the content for your About Us, Contact, Shipping, Return Policies, and Terms of Service pages.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.storefront.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <!-- Left Side: About & Contact -->
            <div class="col-lg-8">
                <!-- Storefront Branding & Customization -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-palette me-2 shopify-green"></i>
                            <h5 class="m-0 fw-bold">Storefront Branding & Colors</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Store Logo -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">Store Logo (Square format recommended)</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded border p-2 bg-light d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; overflow: hidden;">
                                    @if($tenant->logo)
                                        <img id="logo_preview" src="{{ Storage::url($tenant->logo) }}" class="w-100 h-100 object-fit-contain">
                                    @else
                                        <div id="logo_placeholder" class="text-secondary fw-bold">No Logo</div>
                                        <img id="logo_preview" class="d-none w-100 h-100 object-fit-contain">
                                    @endif
                                </div>
                                <div>
                                    <input type="file" name="logo" id="store_logo" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg, image/svg+xml, image/webp" onchange="previewStoreLogo(this)">
                                    <div class="form-text mt-1 small text-muted">Max size: 2MB. SVG, PNG, JPG, or WebP.</div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Theme Colors -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-3">Theme Colors</label>
                            
                            <!-- Color Presets -->
                            <div class="mb-3">
                                <span class="small text-muted d-block mb-2">COLOR PRESETS</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" onclick="applyPreset('#10B981', '#064E3B', '#ECFDF5')">
                                        <span style="width: 12px; height: 12px; border-radius: 50%; background: #10B981; display: inline-block;"></span> Emerald (Default)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" onclick="applyPreset('#4F46E5', '#0F172A', '#E0E7FF')">
                                        <span style="width: 12px; height: 12px; border-radius: 50%; background: #4F46E5; display: inline-block;"></span> Indigo Def
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" onclick="applyPreset('#FF6B35', '#2B2D42', '#F7FFF7')">
                                        <span style="width: 12px; height: 12px; border-radius: 50%; background: #FF6B35; display: inline-block;"></span> Edu Orange
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-2" onclick="applyPreset('#1D4ED8', '#1E3A8A', '#EFF6FF')">
                                        <span style="width: 12px; height: 12px; border-radius: 50%; background: #1D4ED8; display: inline-block;"></span> Royal Blue
                                    </button>
                                </div>
                            </div>

                            <!-- Color Pickers -->
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="primary_color" class="form-label small fw-medium text-secondary">PRIMARY COLOR</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" id="primary_color_picker" value="{{ old('primary_color', $tenant->primary_color ?? '#10b981') }}" oninput="updateColorText(this, 'primary_color')">
                                        <input type="text" name="primary_color" id="primary_color" class="form-control" value="{{ old('primary_color', $tenant->primary_color ?? '#10b981') }}" oninput="updateColorPicker(this, 'primary_color_picker')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="dark_color" class="form-label small fw-medium text-secondary">NAVY COLOR (DARK)</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" id="dark_color_picker" value="{{ old('dark_color', $tenant->dark_color ?? '#0f172a') }}" oninput="updateColorText(this, 'dark_color')">
                                        <input type="text" name="dark_color" id="dark_color" class="form-control" value="{{ old('dark_color', $tenant->dark_color ?? '#0f172a') }}" oninput="updateColorPicker(this, 'dark_color_picker')">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="accent_color" class="form-label small fw-medium text-secondary">ACCENT COLOR (LIGHT)</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color border-end-0" id="accent_color_picker" value="{{ old('accent_color', $tenant->accent_color ?? '#ecfdf5') }}" oninput="updateColorText(this, 'accent_color')">
                                        <input type="text" name="accent_color" id="accent_color" class="form-control" value="{{ old('accent_color', $tenant->accent_color ?? '#ecfdf5') }}" oninput="updateColorPicker(this, 'accent_color_picker')">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Regional & Currency -->
                        <div class="mb-2">
                            <label for="currency" class="form-label fw-semibold">Default Currency</label>
                            <select class="form-select @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                <option value="INR" {{ old('currency', $tenant->currency ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹) - Indian Rupee</option>
                                <option value="USD" {{ old('currency', $tenant->currency ?? 'INR') == 'USD' ? 'selected' : '' }}>USD ($) - US Dollar</option>
                                <option value="EUR" {{ old('currency', $tenant->currency ?? 'INR') == 'EUR' ? 'selected' : '' }}>EUR (€) - Euro</option>
                                <option value="GBP" {{ old('currency', $tenant->currency ?? 'INR') == 'GBP' ? 'selected' : '' }}>GBP (£) - British Pound</option>
                                <option value="AED" {{ old('currency', $tenant->currency ?? 'INR') == 'AED' ? 'selected' : '' }}>AED (د.إ) - UAE Dirham</option>
                                <option value="CAD" {{ old('currency', $tenant->currency ?? 'INR') == 'CAD' ? 'selected' : '' }}>CAD ($) - Canadian Dollar</option>
                                <option value="AUD" {{ old('currency', $tenant->currency ?? 'INR') == 'AUD' ? 'selected' : '' }}>AUD ($) - Australian Dollar</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- About Page Settings -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fa-solid fa-address-card me-2 shopify-green"></i>
                        <h5 class="m-0 fw-bold">About Us Page Content</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="about_title" class="form-label fw-semibold">About Page Hero Title</label>
                            <input type="text" class="form-control @error('about_title') is-invalid @enderror" id="about_title" name="about_title" value="{{ old('about_title', $tenant->about_title) }}" required>
                            @error('about_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="about_text" class="form-label fw-semibold">About Page Main Description</label>
                            <textarea class="form-control @error('about_text') is-invalid @enderror" id="about_text" name="about_text" rows="8" placeholder="Tell your customers about your farm-to-table journey, organic certifications, quality standards, etc.">{{ old('about_text', $tenant->about_text) }}</textarea>
                            @error('about_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Policy Pages Settings -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3">
                        <i class="fa-solid fa-shield-halved me-2 shopify-green"></i>
                        <h5 class="m-0 d-inline-block fw-bold">Store Policies</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="shipping_policy" class="form-label fw-semibold">Shipping & Delivery Policy</label>
                            <textarea class="form-control @error('shipping_policy') is-invalid @enderror" id="shipping_policy" name="shipping_policy" rows="5" placeholder="Details about delivery timeframes (e.g. 2 hours express), charges, regions covered, etc.">{{ old('shipping_policy', $tenant->shipping_policy) }}</textarea>
                            @error('shipping_policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="return_policy" class="form-label fw-semibold">Return & Refund Policy</label>
                            <textarea class="form-control @error('return_policy') is-invalid @enderror" id="return_policy" name="return_policy" rows="5" placeholder="Information about returns guarantee (e.g. refund at door if quality isn't up to standard), processing time, etc.">{{ old('return_policy', $tenant->return_policy) }}</textarea>
                            @error('return_policy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="terms_of_service" class="form-label fw-semibold">Terms of Service</label>
                            <textarea class="form-control @error('terms_of_service') is-invalid @enderror" id="terms_of_service" name="terms_of_service" rows="5" placeholder="General rules, pricing updates, and legal usage conditions.">{{ old('terms_of_service', $tenant->terms_of_service) }}</textarea>
                            @error('terms_of_service')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Contact Details & Submit -->
            <div class="col-lg-4">
                <!-- Contact Info Settings -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fa-solid fa-envelope-open-text me-2 shopify-green"></i>
                        <h5 class="m-0 fw-bold">Contact Page Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="contact_email" class="form-label fw-semibold">Support Email Address</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $tenant->contact_email) }}" placeholder="e.g. support@yourstore.com">
                            @error('contact_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="contact_phone" class="form-label fw-semibold">Support Phone Number</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $tenant->contact_phone) }}" placeholder="e.g. +91 98765 43210">
                            @error('contact_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="whatsapp_number" class="form-label fw-semibold">WhatsApp Number</label>
                            <input type="text" class="form-control @error('whatsapp_number') is-invalid @enderror" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $tenant->whatsapp_number) }}" placeholder="e.g. 919876543210 (without +)">
                            @error('whatsapp_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-1 small text-muted">Include country code without '+' (e.g., 919876543210 for India).</div>
                        </div>
                        <div class="mb-3">
                            <label for="contact_address" class="form-label fw-semibold">Physical Shop Address</label>
                            <textarea class="form-control @error('contact_address') is-invalid @enderror" id="contact_address" name="contact_address" rows="4" placeholder="Store address details...">{{ old('contact_address', $tenant->contact_address) }}</textarea>
                            @error('contact_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Mobile Layout Settings -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fa-solid fa-mobile-screen-button me-2 shopify-green"></i>
                        <h5 class="m-0 fw-bold">Mobile View Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="mobile_grid_cols" class="form-label fw-semibold">Products per Row on Mobile</label>
                            <select class="form-select @error('mobile_grid_cols') is-invalid @enderror" id="mobile_grid_cols" name="mobile_grid_cols" required>
                                <option value="2" {{ old('mobile_grid_cols', $tenant->mobile_grid_cols ?? 2) == 2 ? 'selected' : '' }}>2 Products per Row (Default)</option>
                                <option value="1" {{ old('mobile_grid_cols', $tenant->mobile_grid_cols ?? 2) == 1 ? 'selected' : '' }}>1 Product per Row</option>
                                <option value="3" {{ old('mobile_grid_cols', $tenant->mobile_grid_cols ?? 2) == 3 ? 'selected' : '' }}>3 Products per Row</option>
                            </select>
                            @error('mobile_grid_cols')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2 small text-muted">
                                Select how product cards should align when viewed on mobile screens.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Settings -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fa-solid fa-truck-fast me-2 shopify-green"></i>
                        <h5 class="m-0 fw-bold">Delivery Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="delivery_days" class="form-label fw-semibold">Delivery Days Count</label>
                            <input type="number" class="form-control @error('delivery_days') is-invalid @enderror" id="delivery_days" name="delivery_days" value="{{ old('delivery_days', $tenant->delivery_days ?? 2) }}" min="0" max="30" placeholder="e.g. 2">
                            @error('delivery_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2 small text-muted">
                                Number of days for delivery. The expected delivery date will be calculated from today and shown on product pages. Set to 0 for same-day delivery.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="delivery_info" class="form-label fw-semibold">Delivery Details Text</label>
                            <textarea class="form-control @error('delivery_info') is-invalid @enderror" id="delivery_info" name="delivery_info" rows="3" placeholder="e.g. We deliver fresh groceries directly to your home within 2 hours. Free shipping applies to all orders over ₹499.">{{ old('delivery_info', $tenant->delivery_info) }}</textarea>
                            @error('delivery_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2 small text-muted">
                                This text will be displayed in the "Delivery Details" section on the product page.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax Configuration -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fa-solid fa-percent me-2 shopify-green"></i>
                        <h5 class="m-0 fw-bold">Tax Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="tax_name" class="form-label fw-semibold">Tax Name (e.g. VAT, GST, Sales Tax)</label>
                            <input type="text" class="form-control @error('tax_name') is-invalid @enderror" id="tax_name" name="tax_name" value="{{ old('tax_name', $tenant->tax_name) }}" placeholder="e.g. VAT, GST (Leave empty to disable)">
                            @error('tax_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="tax_rate" class="form-label fw-semibold">Tax Rate (%)</label>
                            <input type="number" step="0.01" class="form-control @error('tax_rate') is-invalid @enderror" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $tenant->tax_rate) }}" placeholder="e.g. 5.00, 18.00 (Leave empty or 0 to disable)">
                            @error('tax_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2 small text-muted">
                                Specified tax rate will be dynamically calculated and displayed as a separate line item at checkout.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Checkout Constraints -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header py-3 d-flex align-items-center">
                        <i class="fa-solid fa-money-bill-wave me-2 shopify-green"></i>
                        <h5 class="m-0 fw-bold">Checkout Constraints</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="min_order_value" class="form-label fw-semibold">Minimum Order Value</label>
                            <input type="number" step="0.01" class="form-control @error('min_order_value') is-invalid @enderror" id="min_order_value" name="min_order_value" value="{{ old('min_order_value', $tenant->min_order_value) }}" placeholder="e.g. 500.00 (Leave empty or 0 to disable)">
                            @error('min_order_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2 small text-muted">
                                Customers will not be able to proceed to checkout if their cart total is below this amount.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <button type="submit" class="btn btn-success bg-shopify-green w-100 py-2 fw-semibold">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i>Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function previewStoreLogo(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const placeholder = document.getElementById('logo_placeholder');
                if (placeholder) placeholder.classList.add('d-none');
                
                const img = document.getElementById('logo_preview');
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function applyPreset(primary, dark, accent) {
        document.getElementById('primary_color').value = primary;
        document.getElementById('primary_color_picker').value = primary;
        
        document.getElementById('dark_color').value = dark;
        document.getElementById('dark_color_picker').value = dark;
        
        document.getElementById('accent_color').value = accent;
        document.getElementById('accent_color_picker').value = accent;
    }

    function updateColorText(picker, textId) {
        document.getElementById(textId).value = picker.value.toUpperCase();
    }

    function updateColorPicker(input, pickerId) {
        let val = input.value;
        if (val.startsWith('#') && val.length === 7) {
            document.getElementById(pickerId).value = val;
        }
    }
</script>
@endsection
