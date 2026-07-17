@extends('layouts.admin')

@section('title', 'Create Order')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders', ['tenant' => request()->route('tenant')]) }}" class="text-decoration-none text-secondary small fw-medium">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
    </a>
    <h1 class="h3 fw-bold text-dark mt-2 mb-0">Create Order</h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger px-3 py-2 text-danger small mb-4 border-0 rounded-2" style="background-color: #fff1f0; border-left: 3px solid #ff4d4f !important;">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.orders.store', ['tenant' => request()->route('tenant')]) }}" method="POST" id="createOrderForm">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Customer and Items -->
        <div class="col-lg-8">
            <!-- Customer Information Card -->
            <div class="card border shadow-sm p-4 mb-4" style="border-radius: 8px;">
                <h3 class="h6 fw-bold mb-3">Customer details</h3>
                
                <div class="mb-3">
                    <label class="form-label">Customer Type</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="customer_type" id="type_existing" value="existing" checked style="cursor: pointer;">
                            <label class="form-check-label" for="type_existing" style="cursor: pointer;">Existing Customer</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="customer_type" id="type_new" value="new" style="cursor: pointer;">
                            <label class="form-check-label" for="type_new" style="cursor: pointer;">New / Guest Customer</label>
                        </div>
                    </div>
                </div>

                <!-- Existing Customer Select -->
                <div class="mb-3" id="existing_customer_group">
                    <label for="user_id" class="form-label">Search / Select Customer</label>
                    <select class="form-select" id="user_id" name="user_id" style="border-radius: 6px;">
                        <option value="" disabled selected>Select a registered user...</option>
                        @foreach($customers as $customer)
                            @php
                                $shipping = null;
                                if ($customer->defaultAddress) {
                                    $shipping = $customer->defaultAddress;
                                } elseif ($customer->addresses->count() > 0) {
                                    $shipping = $customer->addresses->first();
                                }
                            @endphp
                            <option value="{{ $customer->id }}" 
                                    data-name="{{ $customer->name }}" 
                                    data-email="{{ $customer->email }}" 
                                    data-phone="{{ $customer->phone ?? '' }}"
                                    data-addr1="{{ $shipping->address ?? '' }}"
                                    data-addr2="{{ $shipping->apartment ?? '' }}"
                                    data-city="{{ $shipping->city ?? '' }}"
                                    data-state="{{ $shipping->state ?? '' }}"
                                    data-postal="{{ $shipping->postal_code ?? '' }}"
                                    data-country="{{ $shipping->country ?? 'India' }}">
                                {{ $customer->name }} ({{ $customer->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="customer_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required placeholder="John Doe">
                    </div>
                    <div class="col-md-4">
                        <label for="customer_email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control" id="customer_email" name="customer_email" required placeholder="john@example.com">
                    </div>
                    <div class="col-md-4">
                        <label for="customer_phone" class="form-label">Phone *</label>
                        <input type="text" class="form-control" id="customer_phone" name="customer_phone" required placeholder="9876543210">
                    </div>
                </div>
            </div>

            <!-- Address Card -->
            <div class="card border shadow-sm p-4 mb-4" style="border-radius: 8px;">
                <h3 class="h6 fw-bold mb-3">Shipping Address</h3>
                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="shipping_address_line1" class="form-label">Address line 1 *</label>
                        <input type="text" class="form-control" id="shipping_address_line1" name="shipping_address_line1" required placeholder="Street address, P.O. box">
                    </div>
                    <div class="col-md-4">
                        <label for="shipping_address_line2" class="form-label">Address line 2 (Optional)</label>
                        <input type="text" class="form-control" id="shipping_address_line2" name="shipping_address_line2" placeholder="Apartment, suite, unit">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="shipping_city" class="form-label">City *</label>
                        <input type="text" class="form-control" id="shipping_city" name="shipping_city" required placeholder="City">
                    </div>
                    <div class="col-md-6">
                        <label for="shipping_state" class="form-label">State *</label>
                        <input type="text" class="form-control" id="shipping_state" name="shipping_state" required placeholder="State / Province">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="shipping_postal_code" class="form-label">Postal / ZIP Code</label>
                        <input type="text" class="form-control" id="shipping_postal_code" name="shipping_postal_code" placeholder="ZIP Code">
                    </div>
                    <div class="col-md-6">
                        <label for="shipping_country" class="form-label">Country *</label>
                        <input type="text" class="form-control" id="shipping_country" name="shipping_country" required value="India">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="billing_same_as_shipping" checked style="cursor: pointer;">
                        <label class="form-check-label" for="billing_same_as_shipping" style="cursor: pointer;">Billing address is the same as shipping address</label>
                    </div>
                </div>

                <!-- Billing Address Section -->
                <div id="billing_address_section" style="display: none;" class="mt-3">
                    <h3 class="h6 fw-bold mb-3 mt-4 pt-3 border-top">Billing Address</h3>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label for="billing_address_line1" class="form-label">Address line 1</label>
                            <input type="text" class="form-control" id="billing_address_line1" name="billing_address_line1" placeholder="Street address, P.O. box">
                        </div>
                        <div class="col-md-4">
                            <label for="billing_address_line2" class="form-label">Address line 2</label>
                            <input type="text" class="form-control" id="billing_address_line2" name="billing_address_line2" placeholder="Apartment, suite, unit">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="billing_city" class="form-label">City</label>
                            <input type="text" class="form-control" id="billing_city" name="billing_city" placeholder="City">
                        </div>
                        <div class="col-md-6">
                            <label for="billing_state" class="form-label">State</label>
                            <input type="text" class="form-control" id="billing_state" name="billing_state" placeholder="State / Province">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="billing_postal_code" class="form-label">Postal / ZIP Code</label>
                            <input type="text" class="form-control" id="billing_postal_code" name="billing_postal_code" placeholder="ZIP Code">
                        </div>
                        <div class="col-md-6">
                            <label for="billing_country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="billing_country" name="billing_country" value="India">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products / Items Card -->
            <div class="card border shadow-sm p-4 mb-4" style="border-radius: 8px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h6 fw-bold mb-0">Products</h3>
                    <button type="button" class="btn btn-sm btn-outline-dark fw-medium" onclick="addOrderItemRow()">
                        <i class="fa-solid fa-plus me-1"></i> Add line item
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="order_items_table">
                        <thead class="text-muted small">
                            <tr>
                                <th style="width: 45%;">Product</th>
                                <th style="width: 20%;">Variant / Size</th>
                                <th style="width: 15%;">Quantity</th>
                                <th style="width: 15%;">Price</th>
                                <th style="width: 5%;" class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Items inserted dynamically -->
                        </tbody>
                    </table>
                </div>

                <!-- Add item warning/empty state -->
                <div id="empty_items_state" class="text-center py-4 text-muted small">
                    No products added to the order yet. Click "Add line item" to add products.
                </div>
            </div>
        </div>

        <!-- Right Column: Totals & Metadata -->
        <div class="col-lg-4">
            <!-- Totals summary card -->
            <div class="card border shadow-sm p-4 mb-4" style="border-radius: 8px;">
                <h3 class="h6 fw-bold mb-3">Summary</h3>
                
                <div class="d-flex justify-content-between mb-2 small text-secondary">
                    <span>Subtotal</span>
                    <span id="summary_subtotal">₹0.00</span>
                </div>
                
                <div class="mb-3">
                    <label for="shipping_cost" class="form-label small text-secondary d-flex justify-content-between">
                        <span>Shipping Cost</span>
                        <span id="display_shipping_cost">₹0.00</span>
                    </label>
                    <input type="number" class="form-control form-control-sm" id="shipping_cost" name="shipping_cost" value="0" min="0" step="1" oninput="calculateTotals()">
                </div>

                <div class="mb-3">
                    <label for="discount_amount" class="form-label small text-secondary d-flex justify-content-between">
                        <span>Discount Amount</span>
                        <span id="display_discount_amount">-₹0.00</span>
                    </label>
                    <input type="number" class="form-control form-control-sm" id="discount_amount" name="discount_amount" value="0" min="0" step="1" oninput="calculateTotals()">
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-0 fw-bold text-dark fs-5">
                    <span>Total</span>
                    <span id="summary_total">₹0.00</span>
                </div>
            </div>

            <!-- Order options card -->
            <div class="card border shadow-sm p-4 mb-4" style="border-radius: 8px;">
                <h3 class="h6 fw-bold mb-3">Status & Payments</h3>

                <div class="mb-3">
                    <label for="status" class="form-label">Fulfillment Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="pending" selected>Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_status" class="form-label">Payment Status</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="pending" selected>Pending</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method">
                        <option value="cod" selected>Cash on Delivery (COD)</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Credit/Debit Card</option>
                        <option value="upi">UPI / Online Wallet</option>
                    </select>
                </div>
            </div>

            <!-- Delivery & Notes Card -->
            <div class="card border shadow-sm p-4 mb-4" style="border-radius: 8px;">
                <h3 class="h6 fw-bold mb-3">Delivery & Notes</h3>
                
                <div class="mb-3">
                    <label for="delivery_date" class="form-label">Delivery Date</label>
                    <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                </div>
                
                <div class="mb-3">
                    <label for="delivery_time_slot" class="form-label">Delivery Time Slot</label>
                    <select class="form-select" id="delivery_time_slot" name="delivery_time_slot">
                        <option value="">Any time</option>
                        <option value="09:00 AM - 01:00 PM">09:00 AM - 01:00 PM</option>
                        <option value="01:00 PM - 05:00 PM">01:00 PM - 05:00 PM</option>
                        <option value="05:00 PM - 09:00 PM">05:00 PM - 09:00 PM</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control" name="notes" id="notes" rows="3" placeholder="Special instructions, delivery requests..."></textarea>
                </div>
            </div>

            <!-- Save buttons -->
            <button type="submit" class="btn btn-dark w-100 py-2.5 fw-bold shadow-sm" style="border-radius: 6px;">
                Create Order
            </button>
        </div>
    </div>
</form>

<!-- Product list helper structure for JavaScript parsing -->
<script>
    const availableProducts = @json($products);
</script>

<script>
    let rowIndex = 0;

    document.addEventListener('DOMContentLoaded', () => {
        // Init table state
        addOrderItemRow();

        // Customer type change handling
        const radExisting = document.getElementById('type_existing');
        const radNew = document.getElementById('type_new');
        const selectCustomerGroup = document.getElementById('existing_customer_group');
        const customerSelect = document.getElementById('user_id');

        radExisting.addEventListener('change', () => {
            selectCustomerGroup.style.display = 'block';
            customerSelect.required = true;
            clearCustomerFields();
        });

        radNew.addEventListener('change', () => {
            selectCustomerGroup.style.display = 'none';
            customerSelect.required = false;
            customerSelect.value = "";
            clearCustomerFields();
        });

        // Customer selection changes
        customerSelect.addEventListener('change', () => {
            const selectedOpt = customerSelect.options[customerSelect.selectedIndex];
            if (selectedOpt && selectedOpt.value !== "") {
                document.getElementById('customer_name').value = selectedOpt.getAttribute('data-name') || '';
                document.getElementById('customer_email').value = selectedOpt.getAttribute('data-email') || '';
                document.getElementById('customer_phone').value = selectedOpt.getAttribute('data-phone') || '';
                
                // Set shipping fields
                document.getElementById('shipping_address_line1').value = selectedOpt.getAttribute('data-addr1') || '';
                document.getElementById('shipping_address_line2').value = selectedOpt.getAttribute('data-addr2') || '';
                document.getElementById('shipping_city').value = selectedOpt.getAttribute('data-city') || '';
                document.getElementById('shipping_state').value = selectedOpt.getAttribute('data-state') || '';
                document.getElementById('shipping_postal_code').value = selectedOpt.getAttribute('data-postal') || '';
                document.getElementById('shipping_country').value = selectedOpt.getAttribute('data-country') || 'India';
            }
        });

        // Billing address toggle
        const billingCheck = document.getElementById('billing_same_as_shipping');
        const billingSection = document.getElementById('billing_address_section');
        billingCheck.addEventListener('change', () => {
            if (billingCheck.checked) {
                billingSection.style.display = 'none';
                setBillingRequired(false);
            } else {
                billingSection.style.display = 'block';
                setBillingRequired(true);
            }
        });
    });

    function clearCustomerFields() {
        document.getElementById('customer_name').value = '';
        document.getElementById('customer_email').value = '';
        document.getElementById('customer_phone').value = '';
        document.getElementById('shipping_address_line1').value = '';
        document.getElementById('shipping_address_line2').value = '';
        document.getElementById('shipping_city').value = '';
        document.getElementById('shipping_state').value = '';
        document.getElementById('shipping_postal_code').value = '';
        document.getElementById('shipping_country').value = 'India';
    }

    function setBillingRequired(required) {
        document.getElementById('billing_address_line1').required = required;
        document.getElementById('billing_city').required = required;
        document.getElementById('billing_state').required = required;
        document.getElementById('billing_postal_code').required = required;
        document.getElementById('billing_country').required = required;
    }

    function addOrderItemRow() {
        document.getElementById('empty_items_state').style.display = 'none';

        const tableBody = document.querySelector('#order_items_table tbody');
        const row = document.createElement('tr');
        row.id = `item_row_${rowIndex}`;
        
        let productOptions = '<option value="" disabled selected>Select product...</option>';
        availableProducts.forEach(prod => {
            productOptions += `<option value="${prod.id}">${prod.title}</option>`;
        });

        row.innerHTML = `
            <td class="px-2">
                <select class="form-select form-select-sm" name="items[${rowIndex}][product_id]" required onchange="handleProductChange(${rowIndex})">
                    ${productOptions}
                </select>
            </td>
            <td class="px-2">
                <select class="form-select form-select-sm" name="items[${rowIndex}][variant_id]" id="variant_select_${rowIndex}" onchange="handleVariantChange(${rowIndex})">
                    <option value="">Base Product</option>
                </select>
            </td>
            <td class="px-2">
                <input type="number" class="form-control form-select-sm" name="items[${rowIndex}][quantity]" value="1" min="1" required oninput="calculateTotals()">
            </td>
            <td class="px-2">
                <input type="number" class="form-control form-select-sm" name="items[${rowIndex}][price]" id="price_input_${rowIndex}" value="0" min="0" step="1" required oninput="calculateTotals()">
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm text-danger" onclick="removeOrderItemRow(${rowIndex})">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        `;

        tableBody.appendChild(row);
        rowIndex++;
        calculateTotals();
    }

    function removeOrderItemRow(index) {
        const row = document.getElementById(`item_row_${index}`);
        if (row) {
            row.remove();
        }

        const tableBodyRows = document.querySelectorAll('#order_items_table tbody tr');
        if (tableBodyRows.length === 0) {
            document.getElementById('empty_items_state').style.display = 'block';
        }

        calculateTotals();
    }

    function handleProductChange(index) {
        const row = document.getElementById(`item_row_${index}`);
        const productSelect = row.querySelector(`select[name="items[${index}][product_id]"]`);
        const variantSelect = document.getElementById(`variant_select_${index}`);
        const priceInput = document.getElementById(`price_input_${index}`);

        const productId = parseInt(productSelect.value);
        const product = availableProducts.find(p => p.id === productId);

        if (product) {
            // Update variants
            variantSelect.innerHTML = '';
            
            if (product.variants && product.variants.length > 0) {
                product.variants.forEach((v, idx) => {
                    const selected = idx === 0 ? 'selected' : '';
                    variantSelect.innerHTML += `<option value="${v.id}" data-price="${v.price}" ${selected}>Size: ${v.size} (₹${v.price})</option>`;
                });
                
                // Set default variant price
                priceInput.value = product.variants[0].price;
            } else {
                variantSelect.innerHTML = '<option value="">Base Product</option>';
                priceInput.value = 0; // Or standard base price if defined
            }
        }
        calculateTotals();
    }

    function handleVariantChange(index) {
        const variantSelect = document.getElementById(`variant_select_${index}`);
        const priceInput = document.getElementById(`price_input_${index}`);
        const selectedOpt = variantSelect.options[variantSelect.selectedIndex];

        if (selectedOpt && selectedOpt.value !== "") {
            priceInput.value = selectedOpt.getAttribute('data-price') || 0;
        }
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        const rows = document.querySelectorAll('#order_items_table tbody tr');
        
        rows.forEach(row => {
            const qtyInput = row.querySelector('input[name*="[quantity]"]');
            const priceInput = row.querySelector('input[name*="[price]"]');
            
            if (qtyInput && priceInput) {
                const qty = parseInt(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                subtotal += qty * price;
            }
        });

        const shipping = parseFloat(document.getElementById('shipping_cost').value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        
        const total = Math.max(0, subtotal + shipping - discount);

        // Display updates
        document.getElementById('summary_subtotal').textContent = `₹${subtotal.toFixed(2)}`;
        document.getElementById('display_shipping_cost').textContent = `₹${shipping.toFixed(2)}`;
        document.getElementById('display_discount_amount').textContent = `-₹${discount.toFixed(2)}`;
        document.getElementById('summary_total').textContent = `₹${total.toFixed(2)}`;
    }
</script>
@endsection
