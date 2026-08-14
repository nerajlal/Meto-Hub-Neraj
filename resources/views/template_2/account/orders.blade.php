@extends('template_2.layouts.app')

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
                            <a href="{{ route('account.orders') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; background: var(--primary-color); color: #fff; font-weight: 700; text-decoration: none;">
                                <i class="fa-solid fa-bag-shopping"></i> My Orders
                            </a>
                        </li>
                        <li style="margin-bottom: 0.5rem;">
                            <a href="{{ route('account.reorder') }}" style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.5rem; border-radius: 1rem; color: var(--text-main); font-weight: 600; text-decoration: none; transition: 0.3s;" onmouseover="this.style.background='rgba(0,0,0,0.05)'" onmouseout="this.style.background='transparent'">
                                <i class="fa-solid fa-rotate-right"></i> Buy It Again
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

        <!-- Main Content -->
        <div class="account-content">
            @forelse($orders as $order)
            <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 2rem; padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 0.5rem;">Order #{{ $order->id }}</span>
                        <p style="font-weight: 700; font-size: 1.1rem; color: var(--primary-color);">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: inline-block; padding: 0.5rem 1rem; border-radius: 9999px; background: {{ $order->status == 'completed' ? '#ecfdf5' : ($order->status == 'pending' ? '#fffbeb' : '#f8fafc') }}; color: {{ $order->status == 'completed' ? '#065f46' : ($order->status == 'pending' ? '#92400e' : '#1e293b') }}; font-size: 0.85rem; font-weight: 700; text-transform: capitalize;">
                            {{ $order->status }}
                        </span>
                        <p style="margin-top: 0.75rem; font-weight: 800; font-size: 1.25rem; color: var(--primary-color);">{{ $currentTenant->currency ?? '₹' }}{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                </div>

                <div class="order-items-list">
                    @foreach($order->items as $item)
                    <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="width: 70px; height: 70px; background: var(--section-bg); border-radius: 1rem; overflow: hidden; flex-shrink: 0;">
                            @php 
                                $itemImg = asset('Images/g-load.webp');
                                if($item->product_id && $item->product) {
                                    $itemImg = $item->product->main_image_url ?? asset('Images/g-load.webp');
                                } elseif($item->bundle_id && $item->bundle) {
                                    $itemImg = $item->bundle->image ? \Illuminate\Support\Facades\Storage::url($item->bundle->image) : asset('Images/g-load.webp');
                                }
                            @endphp
                            <img src="{{ $itemImg }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='{{ asset('Images/g-load.webp') }}'">
                        </div>
                        <div style="flex-grow: 1;">
                            <h4 style="font-weight: 700; color: var(--primary-color); margin-bottom: 0.25rem;">
                                {{ $item->product_id ? ($item->product->title ?? 'Product') : ($item->bundle->title ?? 'Bundle') }}
                            </h4>
                            <p style="font-size: 0.85rem; color: var(--text-muted);">
                                Qty: {{ $item->quantity }} • {{ $item->size ?? 'Standard' }}
                            </p>
                        </div>
                        <div style="font-weight: 700; color: var(--primary-color);">
                            {{ $currentTenant->currency ?? '₹' }}{{ number_format($item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($order->status == 'pending')
                <div style="margin-top: 1rem; padding-top: 1.5rem; border-top: 1px dashed var(--border-color); display: flex; justify-content: space-between; gap: 1rem;">
                    <form action="{{ route('account.orders.reorder', $order) }}" method="POST">
                        @csrf
                        <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 9999px; font-weight: 600; cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                            <i class="fa-solid fa-rotate-right"></i> Reorder
                        </button>
                    </form>
                    <button style="background: transparent; border: 1.5px solid var(--border-color); color: var(--text-muted); padding: 0.6rem 1.2rem; border-radius: 9999px; font-weight: 600; cursor: not-allowed; font-size: 0.85rem;">
                        <i class="fa-solid fa-clock-rotate-left"></i> Awaiting Fulfillment
                    </button>
                </div>
                @else
                <div style="margin-top: 1rem; padding-top: 1.5rem; border-top: 1px dashed var(--border-color);">
                    <form action="{{ route('account.orders.reorder', $order) }}" method="POST">
                        @csrf
                        <button type="submit" style="background: var(--primary-color); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 9999px; font-weight: 600; cursor: pointer; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                            <i class="fa-solid fa-rotate-right"></i> Reorder
                        </button>
                    </form>
                </div>
                @endif
            </div>
            @empty
            <div style="text-align: center; padding: 5rem 2rem; background: var(--section-bg); border-radius: 2.5rem; border: 1px dashed var(--border-color);">
                <i class="fa-solid fa-box-open" style="font-size: 4rem; color: var(--border-color); margin-bottom: 2rem;"></i>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">No orders yet</h3>
                <p style="color: var(--text-muted); margin-top: 1rem; margin-bottom: 2rem;">You haven't placed any orders with us yet. Start exploring our collections!</p>
                <a href="{{ route('velvet.all-products') }}" style="display: inline-block; background: var(--primary-color); color: #fff; text-decoration: none; padding: 1.25rem 2.5rem; border-radius: 9999px; font-weight: 700; transition: 0.3s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    Explore Fragrances
                </a>
            </div>
            @endforelse
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
