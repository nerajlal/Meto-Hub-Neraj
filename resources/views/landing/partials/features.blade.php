  <section class="features" id="features">
    <div class="section-head">
      <span class="eyebrow">Platform</span>
      @php($pageHeadingTag = request()->routeIs('landing.features') ? 'h1' : 'h2')
      <{{ $pageHeadingTag }}>A storefront and the tools to run it</{{ $pageHeadingTag }}>
      <p>Build your brand’s store, organize your catalog, and manage incoming orders in one dashboard.</p>
    </div>
    <div class="feature-grid">
      <!-- <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-shop"></i></span>
        <h3>Multi-Tenant Store Platform</h3>
        <p>Run one merchant, ten merchants, or a whole chain — each with an isolated, secure storefront.</p>
      </div> -->
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-gauge-high"></i></span>
        <h3>Merchant Dashboard</h3>
        <p>One screen for orders, revenue, inventory, and customers — no switching tools.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-cart-shopping"></i></span>
        <h3>Product Management</h3>
        <p>Add, edit, and organize products with variants, images, and categories in seconds.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-boxes-stacked"></i></span>
        <h3>Inventory Control</h3>
        <p>Manage stock for each product variant from your merchant dashboard.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-layer-group"></i></span>
        <h3>Product Collections</h3>
        <p>Group products into collections, from new arrivals to everyday essentials.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-tag"></i></span>
        <h3>Custom Pricing</h3>
        <p>Set unique prices per customer, product, or purchase quantity.</p>
      </div>
      <!-- <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-users"></i></span>
        <h3>Group Pricing</h3>
        <p>Assign customers to pricing groups and update rates for all of them at once.</p>
      </div> -->
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-tags"></i></span>
        <h3>Coupons</h3>
        <p>Create percentage, flat-rate, or free-shipping coupons with usage limits.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-fire"></i></span>
        <h3>Deals &amp; Offers</h3>
        <p>Schedule time-limited deals that apply automatically at checkout.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-chart-line"></i></span>
        <h3>Volume Discounts</h3>
        <p>Reward bulk buyers with tiered pricing based on quantity ordered.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-gift"></i></span>
        <h3>Product Bundles</h3>
        <p>Package products together at a special bundle price.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-palette"></i></span>
        <h3>Theme Marketplace</h3>
        <p>Choose from {{ count(config('storefront_themes')) }} customizable storefront themes for your brand.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-credit-card"></i></span>
        <h3>Checkout &amp; Subscription Billing</h3>
        <p>Offer cash-on-delivery checkout. MetoHub software subscription billing is handled through Razorpay.</p>
      </div>
      <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-globe"></i></span>
        <h3>Custom Domains</h3>
        <p>Connect your custom domain on Pro or Enterprise. Configure DNS and HTTPS with your hosting provider.</p>
      </div>
      <!-- <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-mobile-screen-button"></i></span><h3>Responsive Themes</h3><p>Every theme looks sharp on desktop, tablet, and mobile out of the box.</p></div> -->
      <!-- <div class="feature-card"><span class="f-icon"><i class="fa-solid fa-user-lock"></i></span>
        <h3>Customer Accounts</h3>
        <p>Let shoppers save addresses, track orders, and reorder favorites.</p>
      </div> -->
    </div>
  </section>

  <!-- ================= PRICING ENGINE ================= -->
  <section class="product-mgmt">
    <div class="pm-visual">
      <div class="pm-card">
        <div class="pm-row"><span><i class="fa-solid fa-leaf"></i> Organic Spinach</span><span class="pm-stock ok">312
            in stock</span></div>
        <div class="pm-row"><span><i class="fa-solid fa-bread-slice"></i> Multigrain Bread</span><span
            class="pm-stock low">8 in stock</span></div>
        <div class="pm-row"><span><i class="fa-solid fa-cheese"></i> Dairy Bundle</span><span class="pm-stock ok">140 in
            stock</span></div>
        <div class="pm-row"><span><i class="fa-solid fa-apple-whole"></i> Apple Crate (10kg)</span><span
            class="pm-stock ok">76 in stock</span></div>
        <div class="pm-search"><span><i class="fa-solid fa-magnifying-glass"></i></span> Search your products…</div>
      </div>
    </div>
    <div class="pm-copy">
      <span class="eyebrow">Product management · illustrative example</span>
      <h2>Manage your entire catalog from one screen</h2>
      <div class="pm-tags">
        <span>Products</span><span>Inventory</span><span>Collections</span><span>Variants</span>
        <span>Variant Stock</span><span>Import / Export</span><span>Bulk Upload</span><span>Search</span>
      </div>
    </div>
  </section>

  <section class="section-head">
    <h2>Find the right fit for your brand.</h2>
    <p><a href="{{ route('landing.themes') }}">Explore storefront themes</a> · <a href="{{ route('landing.pricing') }}">Compare plans</a> · <a href="{{ route('landing.contact') }}">Ask a question</a></p>
  </section>
