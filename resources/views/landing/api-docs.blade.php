@extends('layouts.landing')
@section('content')
  <section class="features" style="padding-top: 150px; padding-bottom: 100px; background: var(--bg);">
    <div class="section-head">
      <span class="eyebrow">Developers</span>
      <h2>API Documentation</h2>
      <p>Build custom integrations and extend the Slot Store platform.</p>
    </div>
    
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 5vw; display: flex; gap: 60px; flex-wrap: wrap;">
      
      <!-- Left Sidebar / Navigation Simulation -->
      <div style="flex: 1; min-width: 250px;">
        <div style="position: sticky; top: 120px;">
          <h4 style="color: var(--muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; font-weight: 600;">Overview</h4>
          <ul style="list-style: none; padding: 0; margin: 0 0 30px 0;">
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--primary); text-decoration: none; font-weight: 600;">Introduction</a></li>
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--dark); text-decoration: none; font-weight: 500;">Authentication</a></li>
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--dark); text-decoration: none; font-weight: 500;">Rate Limits</a></li>
          </ul>

          <h4 style="color: var(--muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; font-weight: 600;">Resources</h4>
          <ul style="list-style: none; padding: 0; margin: 0;">
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--dark); text-decoration: none; font-weight: 500;">Products API</a></li>
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--dark); text-decoration: none; font-weight: 500;">Orders API</a></li>
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--dark); text-decoration: none; font-weight: 500;">Customers API</a></li>
            <li style="margin-bottom: 12px;"><a href="#" style="color: var(--dark); text-decoration: none; font-weight: 500;">Webhooks</a></li>
          </ul>
        </div>
      </div>

      <!-- Right Content Area -->
      <div style="flex: 3; min-width: 280px;">
        
        <div class="feature-card" style="padding: 40px; margin-bottom: 40px;">
          <h3 style="color: var(--dark); margin-top: 0; margin-bottom: 20px; font-size: 1.8rem;">Introduction</h3>
          <p style="color: var(--muted); line-height: 1.8; margin-bottom: 0;">
            The Slot Store API is organized around REST. Our API has predictable resource-oriented URLs, accepts form-encoded request bodies, returns JSON-encoded responses, and uses standard HTTP response codes, authentication, and verbs.
          </p>
        </div>

        <div class="feature-card" style="padding: 40px; margin-bottom: 40px;">
          <h3 style="color: var(--dark); margin-top: 0; margin-bottom: 20px; font-size: 1.8rem;">Authentication</h3>
          <p style="color: var(--muted); line-height: 1.8; margin-bottom: 25px;">
            Authenticate your account when using the API by including your secret API key in the request. You can manage your API keys in the Developer Settings of your dashboard.
          </p>
          
          <div style="background: var(--dark); border-radius: 8px; padding: 20px; overflow-x: auto;">
            <code style="color: #e6edf3; font-family: monospace; font-size: 0.95rem;">
              <span style="color: #ff7b72;">curl</span> https://api.slotstore.com/v1/products \<br>
              &nbsp;&nbsp;-H <span style="color: #a5d6ff;">"Authorization: Bearer sk_live_your_secret_key"</span>
            </code>
          </div>
        </div>

        <div class="feature-card" style="padding: 40px;">
          <h3 style="color: var(--dark); margin-top: 0; margin-bottom: 20px; font-size: 1.8rem;">Sample Response (JSON)</h3>
          <p style="color: var(--muted); line-height: 1.8; margin-bottom: 25px;">
            Successful responses will always be in JSON format. Here is an example response for fetching a product list:
          </p>
          
          <div style="background: var(--dark); border-radius: 8px; padding: 20px; overflow-x: auto;">
            <pre style="margin: 0; color: #e6edf3; font-family: monospace; font-size: 0.9rem; line-height: 1.5;"><code>{
  "object": "list",
  "url": "/v1/products",
  "has_more": false,
  "data": [
    {
      "id": "prod_12345",
      "object": "product",
      "name": "Organic Bananas",
      "price": 2.99,
      "currency": "usd",
      "active": true
    }
  ]
}</code></pre>
          </div>
        </div>

      </div>

    </div>
  </section>
@endsection
