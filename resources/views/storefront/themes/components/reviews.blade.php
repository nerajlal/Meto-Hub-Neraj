@if(isset($reviews) && $reviews->isNotEmpty())
<section class="sf-reviews-section" style="margin-top: 3.5rem; margin-bottom: 3.5rem; position: relative;">
    <div class="sf-section-heading" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color, #e2e8f0); padding-bottom: 0.75rem;">
        <div>
            <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.1em; text-transform: uppercase; color: var(--accent-color, #16a34a); display: block; margin-bottom: 0.25rem;">CUSTOMER VOICES</span>
            <h2 style="font-size: 1.6rem; font-weight: 800; color: var(--primary-color, #1e293b); margin: 0; font-family: inherit;">Loved by our Customers</h2>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.85rem; color: #f59e0b; font-weight: 700;">
                <i class="fa-solid fa-star"></i>
                <span style="color: var(--text-color, #334155);">Verified Buyer Reviews</span>
            </div>
            <div class="sf-reviews-nav-btns" style="display: flex; gap: 0.5rem;">
                <button type="button" class="sf-review-prev-btn" aria-label="Previous reviews" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color, #cbd5e1); background: #ffffff; color: var(--primary-color, #0f172a); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;">
                    <i class="fa-solid fa-chevron-left" style="font-size: 0.75rem;"></i>
                </button>
                <button type="button" class="sf-review-next-btn" aria-label="Next reviews" style="width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--border-color, #cbd5e1); background: #ffffff; color: var(--primary-color, #0f172a); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s ease;">
                    <i class="fa-solid fa-chevron-right" style="font-size: 0.75rem;"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        .sf-reviews-marquee-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 0.5rem 0 1rem;
            -webkit-mask-image: linear-gradient(to right, transparent 0%, black 2%, black 98%, transparent 100%);
            mask-image: linear-gradient(to right, transparent 0%, black 2%, black 98%, transparent 100%);
        }
        .sf-reviews-track {
            display: flex;
            gap: 1.5rem;
            width: max-content;
            animation: sfReviewsTicker 38s linear infinite;
        }
        .sf-reviews-marquee-wrapper:hover .sf-reviews-track {
            animation-play-state: paused;
        }
        @keyframes sfReviewsTicker {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        .sf-review-card-item {
            flex: 0 0 340px;
            width: 340px;
            max-width: 85vw;
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border-color, #e2e8f0);
            border-radius: 1.15rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .sf-review-card-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px -4px rgba(0, 0, 0, 0.1);
            border-color: var(--accent-color, #16a34a);
        }
        @media (max-width: 640px) {
            .sf-review-card-item {
                flex: 0 0 280px;
                width: 280px;
                padding: 1.2rem;
            }
        }
    </style>

    <div class="sf-reviews-marquee-wrapper" id="sfReviewsMarquee">
        <div class="sf-reviews-track" id="sfReviewsTrack">
            {{-- Loop twice to create an infinite continuous ticker moving right to left --}}
            @foreach([1, 2] as $loopIteration)
                @foreach($reviews as $review)
                <div class="sf-review-card-item" @if($loopIteration === 2) aria-hidden="true" @endif>
                    <div>
                        <!-- Stars & Rating -->
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                            <div style="display: flex; gap: 0.2rem; color: #f59e0b; font-size: 0.9rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="fa-solid fa-star"></i>
                                    @else
                                        <i class="fa-regular fa-star" style="color: #cbd5e1;"></i>
                                    @endif
                                @endfor
                            </div>
                            <span style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                                {{ $review->created_at ? $review->created_at->diffForHumans() : 'Verified' }}
                            </span>
                        </div>

                        <!-- Review Content -->
                        <p style="font-size: 0.92rem; line-height: 1.55; color: var(--text-color, #334155); margin-bottom: 1rem; font-style: italic; min-height: 48px; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;">
                            "{{ $review->review }}"
                        </p>

                        <!-- Admin Reply (if any) -->
                        @if(!empty($review->admin_reply))
                        <div style="background: rgba(22, 163, 74, 0.06); border-left: 3px solid var(--accent-color, #16a34a); padding: 0.6rem 0.85rem; border-radius: 0.4rem; margin-bottom: 1rem;">
                            <div style="font-size: 0.7rem; font-weight: 700; color: var(--accent-color, #16a34a); margin-bottom: 0.2rem; display: flex; align-items: center; gap: 0.3rem;">
                                <i class="fa-solid fa-reply"></i> Store Response
                            </div>
                            <p style="font-size: 0.8rem; color: #475569; margin: 0; line-height: 1.35;">
                                {{ $review->admin_reply }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Reviewer Meta -->
                    <div style="border-top: 1px solid var(--border-color, #f1f5f9); padding-top: 0.85rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-color, #16a34a), var(--primary-color, #0f172a)); color: #fff; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; text-transform: uppercase; flex-shrink: 0;">
                                {{ mb_substr($review->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 style="font-size: 0.88rem; font-weight: 700; color: var(--primary-color, #0f172a); margin: 0; line-height: 1.2;">
                                    {{ $review->name }}
                                </h4>
                                <span style="font-size: 0.72rem; color: #16a34a; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="fa-solid fa-circle-check" style="font-size: 0.65rem;"></i> Verified Customer
                                </span>
                            </div>
                        </div>

                        @if($review->product)
                        <div style="text-align: right; max-width: 42%;">
                            <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v3.product', ['id' => $review->product->id]) }}" style="font-size: 0.72rem; color: var(--accent-color, #16a34a); text-decoration: none; font-weight: 600; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $review->product->title }}">
                                {{ $review->product->title }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @endforeach
        </div>
    </div>

    <script>
        (function() {
            var wrapper = document.getElementById('sfReviewsMarquee');
            var track = document.getElementById('sfReviewsTrack');
            var prevBtn = document.querySelector('.sf-review-prev-btn');
            var nextBtn = document.querySelector('.sf-review-next-btn');
            if (!wrapper || !track) return;

            var currentOffset = 0;
            var isManual = false;

            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    track.style.animationPlayState = 'paused';
                    wrapper.scrollBy({ left: 340, behavior: 'smooth' });
                });
            }
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    track.style.animationPlayState = 'paused';
                    wrapper.scrollBy({ left: -340, behavior: 'smooth' });
                });
            }
        })();
    </script>
</section>
@endif
