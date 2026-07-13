@extends('template_3.layouts.app')

@section('title', 'My Account | ' . ($currentTenant->name ?? 'Store'))

@section('content')
<div class="account-page-container" style="max-width: 1000px; margin: 0 auto; padding: 2rem;">
    <div class="account-header" style="margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);">My Account</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Manage your profile, addresses, and orders.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="padding: 0.75rem 1.5rem; border-radius: 9999px; border: 1.5px solid #ef4444; color: #ef4444; background: transparent; font-weight: 700; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='#ef4444'; this.style.color='#fff'" onmouseout="this.style.background='transparent'; this.style.color='#ef4444'">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
            </button>
        </form>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; color: #065f46; padding: 1.25rem; border-radius: 1rem; border: 1px solid #a7f3d0; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="account-grid-container" style="display: grid; grid-template-columns: 300px 1fr; gap: 4rem; align-items: start;">
        <!-- Sidebar Nav -->
        <div class="account-sidebar" style="position: sticky; top: 7rem;">
            <div style="background: var(--section-bg); padding: 1.5rem; border-radius: 2rem; border: 1px solid var(--border-color);">
                <ul style="list-style: none;">
                    <li style="margin-bottom: 0.5rem;">
                        <a href="{{ route('account.profile') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; background: var(--primary-color); color: #fff; font-weight: 700; text-decoration: none;">
                            <i class="fa-solid fa-user"></i> Profile Info
                        </a>
                    </li>
                    <li style="margin-bottom: 0.5rem;">
                        <a href="{{ route('account.orders') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; color: var(--text-main); font-weight: 600; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='transparent'">
                            <i class="fa-solid fa-bag-shopping"></i> My Orders
                        </a>
                    </li>
                    <!-- Add more as needed -->
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="account-content">
            <!-- Profile Section -->
            <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 2.5rem; padding: 3rem; box-shadow: var(--shadow-sm);">
                <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                    <i class="fa-solid fa-circle-user" style="color: var(--accent-color);"></i>
                    Personal Information
                </h2>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                    <div>
                        <label style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem;">Full Name</label>
                        <p style="font-size: 1.1rem; font-weight: 600; color: var(--primary-color);">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 700; margin-bottom: 0.5rem;">Email Address</label>
                        <p style="font-size: 1.1rem; font-weight: 600; color: var(--primary-color);">{{ $user->email }}</p>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 3rem 0;">

                <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
                    <i class="fa-solid fa-location-dot" style="color: var(--accent-color);"></i>
                    Default Shipping Address
                </h2>

                <form action="{{ route('account.address.update') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="grid-column: span 2;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">Street Address</label>
                            <input type="text" name="address_line1" value="{{ $address->address_line1 ?? '' }}" placeholder="House No, Street, Landmark" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">City</label>
                            <input type="text" name="city" value="{{ $address->city ?? '' }}" placeholder="City" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">State</label>
                            <input type="text" name="state" value="{{ $address->state ?? '' }}" placeholder="State" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">PIN Code</label>
                            <input type="text" name="zip" value="{{ $address->zip ?? '' }}" placeholder="6-digit PIN" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none;" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem;">Country</label>
                            <input type="text" name="country" value="{{ $address->country ?? 'India' }}" style="width: 100%; padding: 1rem; border-radius: 1rem; border: 1.5px solid var(--border-color); outline: none; background: var(--section-bg);" readonly>
                        </div>
                    </div>
                    <button type="submit" style="background: var(--primary-color); color: #fff; border: none; padding: 1.25rem 2.5rem; border-radius: 9999px; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 1rem;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    @media (max-width: 768px) {
        .account-page-container {
            padding: 1rem !important;
        }
        .account-header {
            flex-direction: column !important;
            gap: 1rem !important;
            align-items: flex-start !important;
        }
        .account-header h1 {
            font-size: 1.75rem !important;
        }
        .account-header form,
        .account-header button {
            width: 100% !important;
        }
        .account-grid-container {
            grid-template-columns: 1fr !important;
            gap: 2rem !important;
        }
        .account-sidebar {
            position: static !important;
        }
        .account-sidebar div {
            padding: 1rem !important;
        }
        .account-content > div {
            padding: 1.5rem !important;
            border-radius: 1.5rem !important;
        }
        .account-content > div h2 {
            font-size: 1.25rem !important;
            margin-bottom: 1.5rem !important;
        }
        .account-content > div > div:first-of-type {
            grid-template-columns: 1fr !important;
            gap: 1.5rem !important;
            margin-bottom: 2rem !important;
        }
        .account-content form > div {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        .account-content form > div > div:first-child {
            grid-column: span 1 !important;
        }
        .account-content form button {
            width: 100% !important;
        }
    }
    @media (max-width: 480px) {
        .account-header h1 {
            font-size: 1.5rem !important;
        }
        .account-content > div h2 {
            font-size: 1.15rem !important;
        }
    }
</style>
@endsection
