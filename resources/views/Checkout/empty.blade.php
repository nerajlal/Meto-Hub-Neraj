@extends($layout ?? 'template_1.layouts.app')
@section('title', 'Checkout | ' . ($currentTenant->name ?? 'Store'))
@section('content')
<section class="sf-empty-checkout" aria-labelledby="empty-checkout-title" style="max-width:640px;margin:3rem auto;padding:2.5rem 1.5rem;text-align:center;border-radius:16px;background:var(--bg-light,#f5f7ed);color:var(--primary-color,#203311)">
    <i class="fa-solid fa-basket-shopping" aria-hidden="true" style="font-size:2rem;margin-bottom:1rem"></i>
    <h1 id="empty-checkout-title" style="font-size:1.7rem">Your basket is empty</h1>
    <p>Add a product or combo before checking out.</p>
    <a class="sf-primary-button" style="display:inline-block;margin-top:1rem" href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.all-products') }}">Continue shopping →</a>
</section>
@endsection
