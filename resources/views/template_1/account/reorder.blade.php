@extends('template_1.layouts.app')

@section('title', 'My Orders | ' . ($currentTenant->name ?? 'Store'))

@section('content')
<div class="account-page-container" style="max-width: 1000px; margin: 0 auto; padding: 2rem;">
    <div class="account-header" style="margin-bottom: 3rem; display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--primary-color);">My Orders</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Track and view your previous purchases.</p>
        </div>
        <a href="{{ route('account.profile') }}" style="padding: 0.75rem 1.5rem; border-radius: 9999px; border: 1.5px solid var(--border-color); color: var(--text-main); text-decoration: none; font-weight: 700; cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='var(--section-bg)'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-arrow-left"></i> Back to Profile
            </a>
        </div>

        <div class="account-grid-container" style="display: grid; grid-template-columns: 300px 1fr; gap: 4rem; align-items: start;">
            <!-- Sidebar Nav -->
            <div class="account-sidebar" style="position: sticky; top: 7rem;">
                <div style="background: var(--section-bg); padding: 1.5rem; border-radius: 2rem; border: 1px solid var(--border-color);">
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 0.5rem;">
                            <a href="{{ route('account.profile') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; color: var(--text-main); font-weight: 600; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-user"></i> Profile Info
                            </a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="{{ route('account.orders') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; color: var(--text-main); font-weight: 600; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-bag-shopping"></i> My Orders
                            </a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="{{ route('account.reorder') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; background: var(--primary-color); color: #fff; font-weight: 700; text-decoration: none;">
                                <i class="fa-solid fa-rotate-right"></i> Buy It Again
                            </a>
                        </li>
                    </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="account-content">
            <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 2rem; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--primary-color); margin-bottom: 1.5rem;">Previously Ordered Items</h3>
                <div class="product-grid grid-cols-mobile-{{ $currentTenant->mobile_grid_cols ?? 2 }}">
                    @forelse($reorderItems as $item)
                        @if($item->product_id && $item->product)
                            @include('template_1.partials.product_card', ['product' => $item->product])
                        @elseif($item->bundle_id && $item->bundle)
                            @include('template_1.partials.bundle_card', ['bundle' => $item->bundle])
                        @endif
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem 1rem;">
                            <i class="fa-solid fa-basket-shopping" style="font-size: 3rem; color: var(--border-color); margin-bottom: 1rem;"></i>
                            <p style="color: var(--text-muted);">You haven't ordered any items yet.</p>
                        </div>
                    @endforelse
                </div>
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
        .account-header a {
            width: 100% !important;
            text-align: center !important;
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
            padding: 1.25rem !important;
            border-radius: 1.25rem !important;
        }
        .account-content > div > div:first-child {
            flex-direction: column !important;
            gap: 1rem !important;
        }
        .account-content > div > div:first-child div:last-child {
            text-align: left !important;
            width: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        .order-items-list > div {
            flex-wrap: wrap !important;
        }
        .order-items-list > div > div:first-child {
            width: 60px !important;
            height: 60px !important;
        }
        .order-items-list > div > div:last-child {
            margin-left: auto !important;
        }
        .account-content > div > div:last-child,
        .account-content > div > div:nth-child(3) {
            flex-direction: column !important;
        }
        .account-content > div > div:last-child button,
        .account-content > div > div:nth-child(3) button {
            width: 100% !important;
        }
    }
    @media (max-width: 480px) {
        .account-header h1 {
            font-size: 1.5rem !important;
        }
        .account-content > div > div:first-child p {
            font-size: 1rem !important;
        }
        .account-content > div > div:first-child span:last-of-type {
            font-size: 1.1rem !important;
        }
        .order-items-list > div h4 {
            font-size: 0.95rem !important;
        }
        .order-items-list > div p {
            font-size: 0.8rem !important;
        }
    }
</style>
@endsection
