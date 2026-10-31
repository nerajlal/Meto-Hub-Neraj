@if(isset($reviews) && $reviews->isNotEmpty())
<section class="sf-reviews-section" style="margin-top: 3.5rem; margin-bottom: 3.5rem;">
    <div class="sf-section-heading" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.75rem; border-bottom: 2px solid var(--border-color, #e2e8f0); padding-bottom: 0.75rem;">
        <div>
            <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent-color, #16a34a); display: block; margin-bottom: 0.25rem;">CUSTOMER VOICES</span>
            <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--primary-color, #1e293b); margin: 0; font-family: inherit;">Loved by our Customers</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: #f59e0b; font-weight: 700;">
            <i class="fa-solid fa-star"></i>
            <span style="color: var(--text-color, #334155);">Verified Buyer Reviews</span>
        </div>
    </div>

    <div class="sf-reviews-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
        @foreach($reviews as $review)
        <div class="sf-review-card" style="background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #e2e8f0); border-radius: 1rem; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.05); transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div>
                <!-- Stars & Rating -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; gap: 0.2rem; color: #f59e0b; font-size: 0.9rem;">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->rating)
                                <i class="fa-solid fa-star"></i>
                            @else
                                <i class="fa-regular fa-star text-muted" style="color: #cbd5e1;"></i>
                            @endif
                        @endfor
                    </div>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">
                        {{ $review->created_at ? $review->created_at->diffForHumans() : 'Verified' }}
                    </span>
                </div>

                <!-- Review Content -->
                <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-color, #334155); margin-bottom: 1.25rem; font-style: italic;">
                    "{{ $review->review }}"
                </p>

                <!-- Admin Reply (if any) -->
                @if(!empty($review->admin_reply))
                <div style="background: rgba(22, 163, 74, 0.06); border-left: 3px solid var(--accent-color, #16a34a); padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent-color, #16a34a); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.35rem;">
                        <i class="fa-solid fa-reply"></i> Store Response
                    </div>
                    <p style="font-size: 0.85rem; color: #475569; margin: 0; line-height: 1.4;">
                        {{ $review->admin_reply }}
                    </p>
                </div>
                @endif
            </div>

            <!-- Reviewer Meta -->
            <div style="border-top: 1px solid var(--border-color, #f1f5f9); padding-top: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.65rem;">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-color, #16a34a), var(--primary-color, #0f172a)); color: #fff; font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; justify-content: center; text-transform: uppercase;">
                        {{ mb_substr($review->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 style="font-size: 0.9rem; font-weight: 700; color: var(--primary-color, #0f172a); margin: 0; line-height: 1.2;">
                            {{ $review->name }}
                        </h4>
                        <span style="font-size: 0.75rem; color: #16a34a; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                            <i class="fa-solid fa-circle-check" style="font-size: 0.7rem;"></i> Verified Customer
                        </span>
                    </div>
                </div>

                @if($review->product)
                <div style="text-align: right; max-width: 45%;">
                    <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v3.product', ['id' => $review->product->id]) }}" style="font-size: 0.75rem; color: var(--accent-color, #16a34a); text-decoration: none; font-weight: 600; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $review->product->title }}">
                        {{ $review->product->title }}
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif
