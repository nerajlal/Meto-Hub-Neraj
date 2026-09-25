@extends('layouts.admin')

@section('title', 'Carted Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-primary">Carted Products</h1>
    <span class="text-muted small">Active Shopping Carts from Clients</span>
</div>

<div class="card border shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase small fw-medium text-muted border-bottom">
                <tr>
                    <th class="px-3 py-3">Client</th>
                    <th class="px-3 py-3">Cart Items</th>
                    <th class="px-3 py-3 text-end">Est. Total</th>
                    <th class="px-3 py-3 text-end">Last Updated</th>
                </tr>
            </thead>
            <tbody class="border-top-0">
                @forelse($usersWithCarts as $user)
                <tr>
                    <td class="px-3 py-3 align-top" style="width: 250px;">
                        <a href="{{ route('admin.customers.show', $user->id) }}" class="d-flex align-items-center gap-3 text-decoration-none text-dark">
                            <div class="d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 rounded-circle text-secondary fw-bold" style="width: 40px; height: 40px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="d-flex flex-column">
                                <span class="fw-medium text-dark hover-text-primary">{{ $user->name }}</span>
                                <span class="small text-muted">{{ $user->email }}</span>
                                @if($user->phone_number)
                                    <span class="small text-muted">{{ $user->phone_number }}</span>
                                @endif
                            </div>
                        </a>
                    </td>
                    <td class="px-3 py-3">
                        <div class="d-flex flex-column gap-2">
                            @foreach($user->cart as $item)
                                @php
                                    $price = 0;
                                    $image = '';
                                    $title = '';
                                    
                                    if($item->bundle) {
                                        $price = $item->bundle->total_price;
                                        $image = \Illuminate\Support\Facades\Storage::url($item->bundle->image);
                                        $title = $item->bundle->title;
                                    } elseif($item->product) {
                                        $price = $item->product->starting_price;
                                        if($item->size) {
                                            $variant = $item->product->variants->where('size', $item->size)->first();
                                            if($variant) $price = $variant->price;
                                        }
                                        $image = $item->product->main_image_url;
                                        $title = $item->product->title;
                                    }
                                @endphp
                                <div class="d-flex align-items-center gap-3 border rounded p-2 bg-light bg-opacity-50">
                                    <div class="rounded bg-white border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; overflow: hidden; flex-shrink: 0;">
                                        @if($image)
                                            <img src="{{ $image }}" alt="{{ $title }}" class="w-100 h-100 object-fit-cover">
                                        @else
                                            <i class="fas fa-box text-secondary opacity-50"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-dark small fw-medium">{{ $title }}</span>
                                            <span class="text-dark small fw-bold">x{{ $item->quantity }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small" style="font-size: 0.75rem;">
                                                @if($item->size) Size: {{ $item->size }} @endif
                                                @if($item->bundle) (Bundle) @endif
                                            </span>
                                            <span class="text-secondary small">
                                                 {{ $currentTenant->currency ?? '₹' }}{{ number_format($price * $item->quantity, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-3 py-3 text-end align-top fw-bold text-dark">
                        @php
                            $cartTotal = $user->cart->sum(function($item) {
                                if($item->bundle) {
                                    return $item->bundle->total_price * $item->quantity;
                                } elseif($item->product) {
                                    $price = $item->product->starting_price;
                                    if($item->size) {
                                        $variant = $item->product->variants->where('size', $item->size)->first();
                                        if($variant) $price = $variant->price;
                                    }
                                    return $price * $item->quantity;
                                }
                                return 0;
                            });
                        @endphp
                        {{ $currentTenant->currency ?? '₹' }}{{ number_format($cartTotal, 2) }}
                    </td>
                    <td class="px-3 py-3 text-end align-top text-muted small">
                        {{ $user->cart->first()->updated_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <div class="d-flex flex-column align-items-center">
                            <i class="fas fa-shopping-cart fa-3x mb-3 text-secondary opacity-25"></i>
                            <span>No active carts found from registered clients.</span>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usersWithCarts->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $usersWithCarts->links() }}
    </div>
    @endif
</div>
@endsection
