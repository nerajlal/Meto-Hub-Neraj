    <!-- Instagram Reels Section -->
    @if(isset($reels) && $reels->count() > 0)
    <div class="department-section" style="margin-bottom: 3rem;">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 class="section-title" style="font-weight: 800; font-size: 1.6rem; color: var(--primary-color);"><i class="fab fa-instagram text-danger me-2"></i>Shop Our Reels</h2>
        </div>
        
        <div class="reels-container" style="display: flex; overflow-x: auto; gap: 1.5rem; padding-bottom: 1rem; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
            @foreach($reels as $reel)
            @php
                $instagramPost = \App\Services\InstagramPostUrl::normalize($reel->instagram_url);
                $embedUrl = $instagramPost ? $instagramPost.'embed' : null;
            @endphp
            @continue(!$instagramPost)
            <div class="reel-card" style="flex: 0 0 320px; scroll-snap-align: start; display: flex; flex-direction: column; gap: 1rem; position: relative;">
                
                <!-- Instagram Embed -->
                <div style="width: 320px; height: 540px; background: #000; border-radius: 1rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative;">
                    <!-- Sandbox prevents redirecting the parent page or opening popups -->
                    <iframe src="{{ $embedUrl }}" sandbox="allow-scripts allow-same-origin" width="320" height="540" frameborder="0" scrolling="no" allowtransparency="true" style="border: none; width: 100%; height: 100%;"></iframe>
                </div>

                <!-- Linked Action -->
                @if($reel->link_type === 'product' && $reel->product)
                    <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 1rem; padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <div style="flex-grow: 1; min-width: 0;">
                            <h4 style="font-size: 0.9rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $reel->product->title }}</h4>
                            <span style="font-size: 0.85rem; color: var(--accent-color); font-weight: 700;">{{ $currentTenant->currency ?? '₹' }}{{ number_format($reel->product->discounted_price, 2) }}</span>
                        </div>
                        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v3.product', ['id' => $reel->product->id]) }}" class="btn btn-sm" style="background: var(--accent-color); color: #fff; border-radius: 2rem; padding: 0.4rem 1rem; font-weight: 600; text-decoration: none; font-size: 0.8rem; white-space: nowrap;">Shop</a>
                    </div>
                @elseif($reel->link_type === 'collection' && $reel->collection)
                    <div style="background: #fff; border: 1px solid var(--border-color); border-radius: 1rem; padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <div style="flex-grow: 1; min-width: 0;">
                            <h4 style="font-size: 0.9rem; font-weight: 700; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $reel->collection->name }}</h4>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">View Collection</span>
                        </div>
                        <a href="{{ app(\App\Services\StorefrontTheme::class)->url('v1.collection', ['slug' => $reel->collection->slug]) }}" class="btn btn-sm" style="background: var(--accent-color); color: #fff; border-radius: 2rem; padding: 0.4rem 1rem; font-weight: 600; text-decoration: none; font-size: 0.8rem; white-space: nowrap;">Explore</a>
                    </div>
                @endif
                
            </div>
            @endforeach
        </div>
    </div>
    @endif


