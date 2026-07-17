@extends('layouts.admin')

@section('title', 'Custom Checkout Fields | Admin')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 text-gray-800 fw-bold">Custom Checkout Fields</h1>
                <p class="text-muted mb-0">Configure which additional fields to ask customers during checkout.</p>
            </div>
            <div>
                <button type="submit" form="checkoutFieldsForm" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-save me-2"></i> Save Configurations
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-3">
                <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <form id="checkoutFieldsForm" action="{{ route('admin.settings.checkout-fields.update') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm mb-4 border-0 rounded-4">
                        <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
                            <i class="fa-solid fa-cash-register me-2 shopify-green"></i>
                            <h5 class="m-0 fw-bold">Available Checkout Fields</h5>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover mb-0 align-middle" style="min-width: 600px;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Field Name</th>
                                        <th>Description</th>
                                        <th class="text-center">Enable Field</th>
                                        <th class="text-center">Make Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- STANDARD FIELDS -->
                                    <tr class="table-light">
                                        <td colspan="4" class="fw-bold text-muted small text-uppercase">Standard Fields</td>
                                    </tr>
                                    <!-- Full Name -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-user text-muted me-2"></i> Full
                                            Name</td>
                                        <td class="text-muted small">Customer's full name.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[name][enabled]" value="1" {{ !isset($fields['name']) || (isset($fields['name']['enabled']) && $fields['name']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[name][required]" value="1" {{ !isset($fields['name']) || (isset($fields['name']['required']) && $fields['name']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Email Address -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-envelope text-muted me-2"></i>
                                            Email Address</td>
                                        <td class="text-muted small">Customer's email address.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[email][enabled]" value="1" {{ !isset($fields['email']) || (isset($fields['email']['enabled']) && $fields['email']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[email][required]" value="1" {{ !isset($fields['email']) || (isset($fields['email']['required']) && $fields['email']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Phone Number -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-phone text-muted me-2"></i> Phone
                                            Number</td>
                                        <td class="text-muted small">Customer's primary phone.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[phone][enabled]" value="1" {{ !isset($fields['phone']) || (isset($fields['phone']['enabled']) && $fields['phone']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[phone][required]" value="1" {{ !isset($fields['phone']) || (isset($fields['phone']['required']) && $fields['phone']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <td colspan="4" class="fw-bold text-muted small text-uppercase">Address Fields</td>
                                    </tr>
                                    <!-- Use Current Location Button -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-location-crosshairs text-muted me-2"></i> GPS Location
                                            Button</td>
                                        <td class="text-muted small">Allows customers to autofill their address using GPS.
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[use_current_location][enabled]" value="1" {{ !isset($fields['use_current_location']) || (isset($fields['use_current_location']['enabled']) && $fields['use_current_location']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <em>N/A</em>
                                        </td>
                                    </tr>
                                    <!-- Street Address -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-home text-muted me-2"></i> Street
                                            Address</td>
                                        <td class="text-muted small">Primary delivery address line.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[address][enabled]" value="1" {{ !isset($fields['address']) || (isset($fields['address']['enabled']) && $fields['address']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[address][required]" value="1" {{ !isset($fields['address']) || (isset($fields['address']['required']) && $fields['address']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- City -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-city text-muted me-2"></i> City
                                        </td>
                                        <td class="text-muted small">Delivery city.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[city][enabled]" value="1" {{ !isset($fields['city']) || (isset($fields['city']['enabled']) && $fields['city']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[city][required]" value="1" {{ !isset($fields['city']) || (isset($fields['city']['required']) && $fields['city']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- State -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-map text-muted me-2"></i> State
                                        </td>
                                        <td class="text-muted small">Delivery state/province.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[state][enabled]" value="1" {{ !isset($fields['state']) || (isset($fields['state']['enabled']) && $fields['state']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[state][required]" value="1" {{ !isset($fields['state']) || (isset($fields['state']['required']) && $fields['state']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- PIN Code -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-location-dot text-muted me-2"></i> PIN Code</td>
                                        <td class="text-muted small">Postal / ZIP code.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[pincode][enabled]" value="1" {{ !isset($fields['pincode']) || (isset($fields['pincode']['enabled']) && $fields['pincode']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[pincode][required]" value="1" {{ !isset($fields['pincode']) || (isset($fields['pincode']['required']) && $fields['pincode']['required']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Delivery Scheduling -->
                                    <tr class="table-light">
                                        <td colspan="4" class="fw-bold text-muted small text-uppercase">Scheduling</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-calendar-days text-muted me-2"></i> Delivery Date</td>
                                        <td class="text-muted small">Allow customers to pick a delivery date.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[delivery_date][enabled]" value="1" {{ !isset($fields['delivery_date']) || (isset($fields['delivery_date']['enabled']) && $fields['delivery_date']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <em>Always Required if Enabled</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-clock text-muted me-2"></i> Delivery Time Slot</td>
                                        <td class="text-muted small">Allow customers to pick a time slot.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[delivery_time_slot][enabled]" value="1" {{ !isset($fields['delivery_time_slot']) || (isset($fields['delivery_time_slot']['enabled']) && $fields['delivery_time_slot']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <em>Always Required if Enabled</em>
                                        </td>
                                    </tr>

                                    <!-- Payment Methods -->
                                    <tr class="table-light">
                                        <td colspan="4" class="fw-bold text-muted small text-uppercase">Payment Methods</td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-money-bill-1-wave text-muted me-2"></i> Cash on Delivery</td>
                                        <td class="text-muted small">Allow customers to pay upon delivery.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[cash_on_delivery][enabled]" value="1" {{ !isset($fields['cash_on_delivery']) || (isset($fields['cash_on_delivery']['enabled']) && $fields['cash_on_delivery']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <em>N/A</em>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-shield-check text-muted me-2"></i> Pay Online</td>
                                        <td class="text-muted small">Allow online payments (requires configured payment gateway).</td>
                                        <td class="text-center">
                                            @php
                                                $paymentConfigured = isset($tenant->settings['razorpay_enabled']) && $tenant->settings['razorpay_enabled'] && !empty($tenant->settings['razorpay_key']);
                                            @endphp
                                            @if($paymentConfigured)
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[pay_online][enabled]" value="1" {{ !isset($fields['pay_online']) || (isset($fields['pay_online']['enabled']) && $fields['pay_online']['enabled']) ? 'checked' : '' }}>
                                            </div>
                                            @else
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch" disabled>
                                            </div>
                                            <br>
                                            <a href="{{ route('admin.settings.storefront') }}" class="small text-danger text-decoration-none">Configure Gateway</a>
                                            @endif
                                        </td>
                                        <td class="text-center text-muted small">
                                            <em>N/A</em>
                                        </td>
                                    </tr>

                                    <!-- CUSTOM EXTENDED FIELDS -->
                                    <tr class="table-light">
                                        <td colspan="4" class="fw-bold text-muted small text-uppercase">Custom Extended
                                            Fields</td>
                                    </tr>
                                    <!-- Company Name -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-building text-muted me-2"></i>
                                            Company Name</td>
                                        <td class="text-muted small">Useful for B2B orders.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[company_name][enabled]" value="1" {{ isset($fields['company_name']['enabled']) && $fields['company_name']['enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[company_name][required]" value="1" {{ isset($fields['company_name']['required']) && $fields['company_name']['required'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Registration Number -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i
                                                class="fa-solid fa-file-invoice-dollar text-muted me-2"></i> Registration Number</td>
                                        <td class="text-muted small">For tax invoice purposes.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[gst_number][enabled]" value="1" {{ isset($fields['gst_number']['enabled']) && $fields['gst_number']['enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[gst_number][required]" value="1" {{ isset($fields['gst_number']['required']) && $fields['gst_number']['required'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Alternate Phone -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-phone-flip text-muted me-2"></i>
                                            Alternate Phone</td>
                                        <td class="text-muted small">Secondary contact number for delivery.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[alternate_phone][enabled]" value="1" {{ isset($fields['alternate_phone']['enabled']) && $fields['alternate_phone']['enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[alternate_phone][required]" value="1" {{ isset($fields['alternate_phone']['required']) && $fields['alternate_phone']['required'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Landmark -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-map-pin text-muted me-2"></i>
                                            Landmark</td>
                                        <td class="text-muted small">Nearby notable place to help find the address.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[landmark][enabled]" value="1" {{ isset($fields['landmark']['enabled']) && $fields['landmark']['enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[landmark][required]" value="1" {{ isset($fields['landmark']['required']) && $fields['landmark']['required'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Order Notes -->
                                    <tr>
                                        <td class="ps-4 fw-semibold"><i class="fa-solid fa-note-sticky text-muted me-2"></i>
                                            Order Notes</td>
                                        <td class="text-muted small">Special delivery or packaging instructions.</td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[order_notes][enabled]" value="1" {{ isset($fields['order_notes']['enabled']) && $fields['order_notes']['enabled'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-inline-block">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="fields[order_notes][required]" value="1" {{ isset($fields['order_notes']['required']) && $fields['order_notes']['required'] ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white py-3 border-top d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fa-solid fa-save me-2"></i> Save Configurations
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4 bg-light rounded-4">
                            <h6 class="fw-bold mb-3"><i class="fa-solid fa-circle-info text-primary me-2"></i>How this works
                            </h6>
                            <p class="text-muted small mb-3">
                                When you enable a field, it will dynamically appear on your storefront's checkout page.
                            </p>
                            <p class="text-muted small mb-0">
                                <strong>Make Required:</strong> If you toggle "Make Required", the customer will not be able
                                to place the order without filling in that specific field.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection