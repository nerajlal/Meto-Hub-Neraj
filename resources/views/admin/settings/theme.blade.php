@extends('layouts.admin')

@section('title', 'Theme Selection')

@section('content')
<div class="container-fluid py-4" style="background: var(--sf-bg); min-height: 100vh;">

    <!-- Premium Header -->
    <div class="mb-4 mb-md-5 position-relative overflow-hidden p-3 p-md-4 rounded-4" style="background: linear-gradient(135deg, #8B5CF6 0%, #6D28D9 100%); border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <!-- Abstract background glow -->
        <div class="position-absolute rounded-circle" style="width: 250px; height: 250px; background: rgba(255, 255, 255, 0.15); filter: blur(80px); top: -100px; right: -50px; pointer-events: none;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.15); filter: blur(60px); bottom: -100px; left: -50px; pointer-events: none;"></div>
        
        <div class="position-relative z-1">
            <span class="badge mb-2 px-3 py-1.5 rounded-pill uppercase tracking-wider text-white-50" style="background: rgba(255,255,255,0.08); font-size: 10px; font-weight: 600; letter-spacing: 1.2px; border: 1px solid rgba(255,255,255,0.12);">ESTHETIC MANAGEMENT</span>
            <h1 class="h2 text-white mb-2 fw-bold" style="font-family: 'Playfair Display', Georgia, serif; letter-spacing: -0.5px;">Storefront Theme Selection</h1>
            <p class="text-white-50 small mb-0 max-w-lg" style="max-width: 600px; line-height: 1.6;">Switch your boutique layout dynamically. Preview the live storefront in real-time using mobile and desktop simulations before activating your design layout.</p>
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

    <!-- Themes Premium Grid -->
    <div class="row g-4">
        @foreach($themes as $theme)
            @php
                $previewUrl = route($theme['preview_route'], ['tenant_id' => $tenant->id]) . '?preview=1&preview_theme=' . $theme['id'];
                $isActive = $tenant->theme === $theme['id'];
                
                // Color badges matching each theme's aesthetic
                $badgeStyles = [
                    'aura_luxe' => 'background: #F9F0FF; color: #7B2CBF; border: 1px solid #E8C8FF;',
                    'velvet_dark' => 'background: #FFF3E0; color: #E65100; border: 1px solid #FFE0B2;',
                    'editorial_cream' => 'background: #F1F8E9; color: #33691E; border: 1px solid #DCEDC8;',
                    'modern_minimal' => 'background: #ECEFF1; color: #263238; border: 1px solid #CFD8DC;'
                ];
                $currentBadgeStyle = $badgeStyles[$theme['id']] ?? 'background: #F1F1F1; color: #6d7175;';
            @endphp
            
            <div class="col-md-6 col-lg-6 col-xl-3">
                <div class="card h-100 luxury-theme-card position-relative {{ $isActive ? 'luxury-theme-card-active' : '' }}">
                    
                    <!-- Top Ribbon for Active Theme -->
                    @if($isActive)
                        <div class="position-absolute top-0 end-0 bg-success text-white px-3 py-1.5 small fw-bold shadow-sm" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%) !important; font-size: 10px; z-index: 5; border-radius: 0 12px 0 12px; letter-spacing: 0.5px;">
                            CURRENT ACTIVE
                        </div>
                    @endif

                    <!-- Theme Preview Screenshot Container -->
                    <div class="position-relative overflow-hidden bg-light" style="height: 200px; border-radius: 12px 12px 0 0;">
                        <img src="{{ asset($theme['preview_img']) }}" class="w-100 h-100 object-fit-cover transition-transform luxury-preview-img" alt="{{ $theme['name'] }} Preview" style="object-position: top center;">
                        <div class="luxury-overlay d-flex flex-column align-items-center justify-content-center p-3 gap-2">
                            <span class="text-white text-center small px-2 mb-2" style="font-weight: 500; font-size: 12px; opacity: 0; transform: translateY(10px); transition: all 0.3s ease;">
                                Experience the design inside our custom device simulator
                            </span>
                            <button type="button" class="btn btn-light btn-sm shadow-sm d-flex align-items-center gap-2 py-2 px-3 fw-medium hover-grow" onclick="openPreviewModal('{{ $previewUrl }}', '{{ $theme['name'] }}')" style="font-size: 12px; border-radius: 20px;">
                                <i class="fas fa-eye text-success" style="font-size: 12px;"></i> View Simulator Preview
                            </button>
                        </div>
                    </div>

                    <!-- Theme Details -->
                    <div class="card-body d-flex flex-column justify-content-between p-4" style="background: #ffffff; border-radius: 0 0 12px 12px;">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h3 class="h6 fw-bold text-primary mb-0" style="font-size: 15px;">{{ $theme['name'] }}</h3>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.6; font-size: 12.5px; height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                {{ $theme['description'] }}
                            </p>
                        </div>
                        
                        <!-- Actions -->
                        <div class="vstack gap-2 mt-4 pt-3 border-top" style="border-color: #f1f1f1 !important;">
                            <button type="button" class="btn btn-outline-dark btn-sm w-100 py-2 d-flex align-items-center justify-content-center gap-2 fw-medium" onclick="openPreviewModal('{{ $previewUrl }}', '{{ $theme['name'] }}')" style="font-size: 12px; border-radius: 8px;">
                                <i class="fas fa-desktop" style="font-size: 11px;"></i> Preview Mockup
                            </button>
                            
                            @if(!$isActive)
                                <form action="{{ route('admin.settings.theme.update') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <input type="hidden" name="theme" value="{{ $theme['id'] }}">
                                    <button type="submit" class="btn btn-primary text-white btn-sm w-100 d-flex align-items-center justify-content-center gap-2 py-2 fw-semibold" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important; border: none !important; font-size: 12px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 128, 96, 0.15);">
                                        <i class="fas fa-power-off" style="font-size: 11px;"></i> Activate Theme
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 py-2 fw-semibold" disabled style="background-color: #f6f6f7; border-color: #e1e3e5; color: #202223; cursor: not-allowed; font-size: 12px; border-radius: 8px;">
                                    <i class="fas fa-check-circle text-success me-1" style="font-size: 12px;"></i> Currently Active
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- TEMPLATE PREVIEW MODAL (Immersive Glassmorphism Styling) -->
<div class="demo-modal-overlay" id="demoModal">
  <button class="demo-modal-close shadow-sm" id="closeDemoBtn" aria-label="Close Preview" onclick="closePreviewModal()">&times;</button>
  
  <!-- Sleek device toggles -->
  <div class="device-toggle-wrapper">
    <button class="toggle-btn active" id="mobileToggle" onclick="setPreviewDevice('mobile')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
      Mobile View
    </button>
    <button class="toggle-btn" id="desktopToggle" onclick="setPreviewDevice('desktop')">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
      Desktop View
    </button>
  </div>
  
  <!-- Mockup frame wrapper with premium goldish/dark edges -->
  <div class="phone-mockup-wrapper" id="phoneMockup">
    <!-- Physical Side Buttons -->
    <div class="phone-button volume-up"></div>
    <div class="phone-button volume-down"></div>
    <div class="phone-button power"></div>
    
    <div class="phone-screen">
      <!-- iOS Status Bar -->
      <div class="phone-status-bar">
        <span class="status-time" id="statusTime">9:41</span>
        <div class="phone-notch">
          <span class="notch-camera"></span>
        </div>
        <div class="status-icons">
          <!-- Network SVG -->
          <svg class="status-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M2 22h20V2z" opacity="0.3"/>
            <path d="M2 22h16V6z"/>
          </svg>
          <!-- Wifi SVG -->
          <svg class="status-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 21a2 2 0 0 1-1.41-.59l-8.5-8.5a1 1 0 0 1 0-1.41A15.91 15.91 0 0 1 12 6a15.91 15.91 0 0 1 9.91 4.5 1 1 0 0 1 0 1.41l-8.5 8.5A2 2 0 0 1 12 21z"/>
          </svg>
          <!-- Battery Percentage -->
          <span class="battery-percentage">88%</span>
          <div class="battery-icon">
            <span class="battery-level"></span>
          </div>
        </div>
      </div>
      
      <!-- Loader Overlay -->
      <div class="phone-loader" id="phoneLoader">
        <div class="spinner"></div>
        <div class="loader-text text-white-50" id="loaderText">Curating storefront...</div>
      </div>
      
      <!-- Live IFrame -->
      <iframe src="" id="demoIframe"></iframe>
      
      <!-- Home Indicator -->
      <div class="phone-home-indicator"></div>
    </div>
  </div>
</div>

<style>
    /* Card luxury theme style additions */
    .luxury-theme-card {
        border: 1px solid #e1e3e5 !important;
        border-radius: 12px !important;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02) !important;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .luxury-theme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.07) !important;
        border-color: #b3b3b3 !important;
    }

    .luxury-theme-card-active {
        border: 2px solid #8b5cf6 !important;
        box-shadow: 0 12px 30px rgba(0, 128, 96, 0.08) !important;
        margin: -1px;
    }
    
    .luxury-preview-img {
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .luxury-theme-card:hover .luxury-preview-img {
        transform: scale(1.04);
    }

    .luxury-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(17, 17, 17, 0.7);
        opacity: 0;
        transition: opacity 0.3s ease;
        backdrop-filter: blur(2px);
    }
    
    .luxury-theme-card:hover .luxury-overlay {
        opacity: 1;
    }

    .luxury-theme-card:hover .luxury-overlay span {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    .hover-grow {
        transition: transform 0.2s ease;
    }
    .hover-grow:hover {
        transform: scale(1.05);
    }

    /* ── IMMERSIVE POPUP PREVIEW MODAL ── */
    .demo-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(10, 10, 10, 0.85);
        backdrop-filter: blur(20px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .demo-modal-overlay.active {
        display: flex;
        opacity: 1;
    }

    .demo-modal-close {
        position: absolute;
        top: 24px;
        right: 24px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        color: #ffffff;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s, transform 0.3s, border-color 0.3s;
        z-index: 2010;
        line-height: 1;
    }

    .demo-modal-close:hover {
        background: rgba(201, 168, 76, 0.2);
        border-color: rgba(201, 168, 76, 0.4);
        color: #c9a84c;
        transform: scale(1.08) rotate(90deg);
    }

    .device-toggle-wrapper {
        position: absolute;
        top: 24px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.12);
        padding: 4px;
        border-radius: 30px;
        display: flex;
        gap: 4px;
        z-index: 2010;
        box-shadow: 0 4px 24px rgba(0,0,0,0.3);
    }

    .toggle-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.65);
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .toggle-btn svg {
        width: 14px;
        height: 14px;
    }

    .toggle-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.04);
    }

    .toggle-btn.active {
        background: #ffffff;
        color: #111111;
        box-shadow: 0 2px 10px rgba(0,0,0,0.25);
    }

    .phone-mockup-wrapper {
        position: relative;
        width: 375px;
        height: 780px;
        background: #09090b;
        border: 12px solid #1c1c1e;
        border-radius: 44px;
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.65), 
                    0 0 0 2px rgba(255, 255, 255, 0.08), 
                    inset 0 0 10px rgba(0, 0, 0, 0.9);
        transform: scale(0.85) translateY(40px);
        transform-origin: center;
        opacity: 0;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease, width 0.3s cubic-bezier(0.16, 1, 0.3, 1), height 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .demo-modal-overlay.active .phone-mockup-wrapper {
        transform: scale(0.92) translateY(0);
        opacity: 1;
    }

    /* Physical buttons details */
    .phone-button {
        position: absolute;
        background: #27272a;
        border-radius: 3px;
        z-index: -1;
    }
    .phone-button.volume-up { left: -15px; top: 140px; width: 3px; height: 45px; }
    .phone-button.volume-down { left: -15px; top: 195px; width: 3px; height: 45px; }
    .phone-button.power { right: -15px; top: 170px; width: 3px; height: 75px; }

    .phone-screen {
        width: 100%;
        height: 100%;
        background: #ffffff;
        border-radius: 32px;
        overflow: hidden;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    /* iOS Status Bar */
    .phone-status-bar {
        position: relative;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        background: #ffffff;
        z-index: 15;
        font-size: 11px;
        font-weight: 600;
        color: #000000;
        user-select: none;
        transition: background-color 0.4s ease, color 0.4s ease;
    }

    .phone-notch {
        position: absolute;
        top: 6px;
        left: 50%;
        transform: translateX(-50%);
        width: 110px;
        height: 24px;
        background: #000000;
        border-radius: 12px;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notch-camera {
        width: 8px;
        height: 8px;
        background: #05051a;
        border-radius: 50%;
        box-shadow: inset 0 0 2px rgba(255,255,255,0.4);
    }

    .status-icons {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .status-icon {
        width: 14px;
        height: 14px;
        fill: currentColor;
    }

    /* Battery percentage and cell indicator */
    .battery-percentage {
        font-size: 10px;
    }
    .battery-icon {
        width: 20px;
        height: 10px;
        border: 1px solid currentColor;
        border-radius: 3px;
        padding: 1px;
        display: inline-flex;
        align-items: center;
        position: relative;
    }
    .battery-level {
        width: 80%;
        height: 100%;
        background: currentColor;
        border-radius: 1px;
    }

    /* Spinner loader for simulation */
    .phone-loader {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #111111;
        z-index: 100;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 16px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .phone-loader.active {
        opacity: 1;
        pointer-events: all;
    }

    .spinner {
        width: 38px;
        height: 38px;
        border: 3px solid rgba(201, 168, 76, 0.1);
        border-top-color: #c9a84c;
        border-radius: 50%;
        animation: spin 0.85s linear infinite;
    }

    .loader-text {
        font-size: 13px;
        font-weight: 500;
        letter-spacing: 0.3px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    #demoIframe {
        width: 100%;
        height: calc(100% - 40px);
        border: none;
        z-index: 5;
    }

    .phone-home-indicator {
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 130px;
        height: 5px;
        background: #000000;
        border-radius: 3px;
        z-index: 15;
    }

    /* ── DESKTOP FRAME MODE OVERRIDES ── */
    .demo-modal-overlay.desktop-mode .phone-mockup-wrapper {
        width: 85%;
        height: 82vh;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        background: #ffffff;
        padding: 0;
        box-shadow: 0 40px 100px rgba(0, 0, 0, 0.75);
        transform: scale(1) translateY(0);
    }

    .demo-modal-overlay.desktop-mode .phone-button,
    .demo-modal-overlay.desktop-mode .phone-status-bar,
    .demo-modal-overlay.desktop-mode .phone-home-indicator,
    .demo-modal-overlay.desktop-mode .phone-notch {
        display: none;
    }

    .demo-modal-overlay.desktop-mode .phone-screen {
        border-radius: 14px;
    }

    .demo-modal-overlay.desktop-mode #demoIframe {
        height: 100%;
    }
</style>

@push('scripts')
<script>
    // Digital clock dynamic updater
    function updateClock() {
        const timeEl = document.getElementById('statusTime');
        if (!timeEl) return;
        const now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        minutes = minutes < 10 ? '0' + minutes : minutes;
        timeEl.textContent = hours + ':' + minutes;
    }
    setInterval(updateClock, 30000);
    updateClock();

    // Theme style mapping for iOS Status Bar colors and loader text
    const themeStyles = {
        'Aura Luxe': { bg: '#1a101e', text: '#ffffff', msg: 'Curating Aura Luxe experience...' },
        'Velvet Dark': { bg: '#1a1208', text: '#ffffff', msg: 'Polishing Velvet Dark storefront...' },
        'Editorial Cream': { bg: '#fdfaf6', text: '#3d2b0e', msg: 'Styling Editorial Cream showcase...' },
        'Modern Minimal': { bg: '#0f1923', text: '#ffffff', msg: 'Aligning Modern Minimal grid...' }
    };

    // Open Modal
    function openPreviewModal(previewUrl, themeName) {
        const modal = document.getElementById('demoModal');
        const iframe = document.getElementById('demoIframe');
        const loader = document.getElementById('phoneLoader');
        const loaderText = document.getElementById('loaderText');
        const statusBar = document.querySelector('.phone-status-bar');

        // Reset to Mobile mode initially
        setPreviewDevice('mobile');

        // Apply theme specific loading configs
        const config = themeStyles[themeName] || { bg: '#ffffff', text: '#000000', msg: 'Curating storefront...' };
        loaderText.textContent = config.msg;
        statusBar.style.backgroundColor = config.bg;
        statusBar.style.color = config.text;

        loader.classList.add('active');
        modal.classList.add('active');
        
        iframe.src = previewUrl;

        // Hide loader when iframe has completed loading
        iframe.onload = function() {
            loader.classList.remove('active');
        };
    }

    // Close Modal
    function closePreviewModal() {
        const modal = document.getElementById('demoModal');
        const iframe = document.getElementById('demoIframe');
        modal.classList.remove('active');
        iframe.src = '';
    }

    // Toggle Preview Devices
    function setPreviewDevice(mode) {
        const modal = document.getElementById('demoModal');
        const btnMobile = document.getElementById('mobileToggle');
        const btnDesktop = document.getElementById('desktopToggle');

        if (mode === 'mobile') {
            modal.classList.remove('desktop-mode');
            btnMobile.classList.add('active');
            btnDesktop.classList.remove('active');
        } else {
            modal.classList.add('desktop-mode');
            btnDesktop.classList.add('active');
            btnMobile.classList.remove('active');
        }
    }

    // Close preview if clicking outside the phone wrapper
    document.getElementById('demoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePreviewModal();
        }
    });
</script>
@endpush
@endsection
