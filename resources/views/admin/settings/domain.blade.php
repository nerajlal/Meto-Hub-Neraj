@extends('layouts.admin')

@section('title', 'Domain Connection Settings')

@section('content')
<div class="container-fluid py-4" style="background: var(--sf-bg); min-height: 100vh;">

    <!-- Premium Header -->
    <div class="mb-4 mb-md-5 position-relative overflow-hidden p-3 p-md-4 rounded-4" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(255, 255, 255, 0.15); filter: blur(80px); top: -100px; right: -50px; pointer-events: none;"></div>
        
        <div class="position-relative z-1">
            <span class="badge mb-2 px-3 py-1.5 rounded-pill uppercase tracking-wider text-white-50" style="background: rgba(255,255,255,0.08); font-size: 10px; font-weight: 600; letter-spacing: 1.2px; border: 1px solid rgba(255,255,255,0.12);">SYSTEM SETTINGS</span>
            <h1 class="h2 text-white mb-2 fw-bold" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.5px;">Domain Connection</h1>
            <p class="text-white-50 small mb-0" style="max-width: 600px; line-height: 1.6;">Connect your own custom domain (e.g., grocery.com) to your brand's store.</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 mb-4 p-3 shadow-sm" role="alert" style="background: #ede9fe; border-left: 5px solid #8b5cf6 !important; color: #6d28d9;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-check-circle" style="color: #8b5cf6; font-size: 18px;"></i>
                <div>
                    <strong class="fw-semibold">Success!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Input Settings Form -->
        <div class="col-lg-7 col-12">
            <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff;">
                <h4 class="h5 fw-bold mb-4 text-primary" style="font-family: 'Playfair Display', serif;">Connect Custom Domain</h4>
                
                @if(($tenant->plan ?? 'base') == 'base')
                    <div class="alert alert-warning border-0 rounded-3 mb-4 p-3 shadow-sm" style="background: #FFFBEB; border-left: 5px solid #F59E0B !important; color: #92400E;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-lock" style="color: #F59E0B;"></i>
                            <div>
                                <strong class="fw-semibold">Custom Domain Locked.</strong> Please <a href="{{ route('admin.billing') }}" class="fw-bold" style="color: #92400E; text-decoration: underline;">upgrade to Pro</a> to connect a custom domain. For now, you can configure your free MetoHub subdomain.
                            </div>
                        </div>
                    </div>
                @endif
                
                <form action="{{ route('admin.settings.domain.update') }}" method="POST">
                    @csrf
                    
                    @if(($tenant->plan ?? 'base') == 'base')
                        <div class="mb-4">
                            <label for="subdomain_input" class="form-label fw-semibold text-dark small">Your Free Subdomain</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 8px 0 0 8px;">https://</span>
                                <input type="text" id="subdomain_input" class="form-control py-2.5 px-3" value="{{ str_replace('.metohub.com', '', old('domain', $tenant->domain)) }}" placeholder="e.g. myshop" style="border-radius: 0;">
                                <span class="input-group-text bg-light text-muted" style="border-radius: 0 8px 8px 0;">.metohub.com</span>
                            </div>
                            <input type="hidden" name="domain" id="domain" value="{{ old('domain', $tenant->domain) }}">
                            <div class="form-text text-muted small mt-2">Choose your free subdomain name.</div>
                            @error('domain')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <script>
                            document.getElementById('subdomain_input').addEventListener('input', function() {
                                document.getElementById('domain').value = this.value ? this.value + '.metohub.com' : '';
                            });
                        </script>
                    @else
                        <div class="mb-4">
                            <label for="domain" class="form-label fw-semibold text-dark small">Your Domain Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted" style="border-radius: 8px 0 0 8px;">https://</span>
                                <input type="text" name="domain" id="domain" class="form-control py-2.5 px-3 @error('domain') is-invalid @enderror" value="{{ old('domain', $tenant->domain) }}" placeholder="e.g. yourbrand.com" style="border-radius: 0 8px 8px 0;">
                            </div>
                            <div class="form-text text-muted small mt-2">Enter your root domain (e.g. myshop.com) or subdomain (e.g. shop.myshop.com) without https:// or slashes.</div>
                            @error('domain')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                        <div>
                            @if($tenant->domain)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 11px;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Connected
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-pill" style="font-size: 11px;">
                                    <i class="fa-solid fa-circle me-1" style="font-size: 8px; opacity: 0.6;"></i> Not Connected
                                </span>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary text-white py-2 px-4 fw-semibold" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; border: none !important; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 128, 96, 0.15);">
                            Save Domain
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions Panel -->
        <div class="col-lg-5 col-12">
            <div class="card border-0 rounded-4 shadow-sm p-4 h-100" style="background: #ffffff; border: 1px solid rgba(0,0,0,0.03) !important;">
                @if(($tenant->plan ?? 'base') == 'base')
                    <h4 class="h5 fw-bold mb-4 text-primary" style="font-family: 'Playfair Display', serif;">Subdomain Setup</h4>
                    <p class="text-secondary small mb-4" style="line-height: 1.6;">Your free subdomain is instantly activated upon saving. You can share this link with your customers immediately to start taking orders.</p>
                @else
                    <h4 class="h5 fw-bold mb-4 text-primary" style="font-family: 'Playfair Display', serif;">DNS Setup Instructions</h4>
                    
                    <p class="text-secondary small mb-4" style="line-height: 1.6;">To connect your custom domain, log in to your domain registrar (GoDaddy, Namecheap, Cloudflare, etc.) and add the following DNS records:</p>
                    
                    <div class="mb-4">
                        <h5 class="small fw-semibold text-dark mb-2">1. Pointing Root Domain (A Record)</h5>
                        <div class="p-3 bg-light rounded-3 border mb-2">
                            <div class="row g-2 text-monospace small">
                                <div class="col-4 text-muted fw-semibold">Type:</div>
                                <div class="col-8 text-dark">A</div>
                                <div class="col-4 text-muted fw-semibold">Host/Name:</div>
                                <div class="col-8 text-dark">@</div>
                                <div class="col-4 text-muted fw-semibold">Value/IP:</div>
                                <div class="col-8 text-dark fw-bold">127.0.0.1</div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h5 class="small fw-semibold text-dark mb-2">2. Pointing Subdomain (CNAME Record)</h5>
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-2 text-monospace small">
                                <div class="col-4 text-muted fw-semibold">Type:</div>
                                <div class="col-8 text-dark">CNAME</div>
                                <div class="col-4 text-muted fw-semibold">Host/Name:</div>
                                <div class="col-8 text-dark">www</div>
                                <div class="col-4 text-muted fw-semibold">Value/Target:</div>
                                <div class="col-8 text-dark fw-bold">metohub.com</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
