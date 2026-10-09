@extends('layouts.admin')
@section('title', 'Storefront Themes')
@section('content')
<div class="theme-settings">
    <div class="theme-settings-heading">
        <div><span class="theme-settings-kicker">MAKE IT YOUR STORE</span><h1>Find your storefront’s style.</h1><p>{{ count($themes) }} designs. The same familiar shopping experience. Preview a theme on your own store before making it live.</p></div>
        <a href="{{ route('v1.home', ['tenant_id' => $tenant->id]) }}" target="_blank" rel="noopener" class="btn btn-outline-dark">Visit your store <i class="fas fa-arrow-up-right-from-square ms-2" aria-hidden="true"></i></a>
    </div>
    @if(session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
    @endif
    <div class="theme-settings-note"><i class="fas fa-sliders" aria-hidden="true"></i><span>Your store’s logo, products, categories, brand colours, cart, and checkout work across every theme.</span></div>
    <div class="theme-selection-grid">
        @foreach($themes as $theme)
            @php
                $isActive = app(\App\Services\StorefrontTheme::class)->normalize($tenant->theme) === $theme['id'];
                $previewUrl = route('v1.home', ['tenant_id' => $tenant->id, 'preview' => 1, 'preview_theme' => $theme['id']]);
            @endphp
            <article class="theme-choice-card {{ $isActive ? 'is-active' : '' }}" style="--choice-tone: {{ $theme['tone'] }}; --choice-bg: {{ $theme['background'] }};">
                <div class="theme-choice-art">
                    <img src="{{ asset($theme['preview_img']) }}" alt="{{ $theme['name'] }} layout illustration" width="640" height="400" loading="lazy">
                    @if($isActive)<span class="theme-active-badge"><i class="fas fa-check" aria-hidden="true"></i> Active theme</span>@endif
                </div>
                <div class="theme-choice-content">
                    <span class="theme-choice-version">{{ $theme['version'] }}</span>
                    <h2>{{ $theme['name'] }}</h2>
                    <p>{{ $theme['description'] }}</p>
                    <div class="theme-choice-actions">
                        <button type="button" class="theme-preview-button" data-preview-url="{{ $previewUrl }}" data-theme-name="{{ $theme['name'] }}"><i class="fas fa-eye" aria-hidden="true"></i> Live preview</button>
                        @if($isActive)
                            <button type="button" class="theme-activate-button" disabled>Currently active</button>
                        @else
                            <form action="{{ route('admin.settings.theme.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="theme" value="{{ $theme['id'] }}">
                                <button type="submit" class="theme-activate-button">Use this theme <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
                            </form>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
<dialog class="theme-preview-dialog" id="theme-preview-dialog" aria-labelledby="theme-preview-title">
    <div class="theme-preview-toolbar">
        <div><strong id="theme-preview-title">Store preview</strong><span>Previewing doesn’t change your active theme.</span></div>
        <div class="theme-preview-devices" role="group" aria-label="Preview screen size">
            <button type="button" data-device="desktop" aria-pressed="true"><i class="fas fa-desktop" aria-hidden="true"></i> Desktop</button>
            <button type="button" data-device="mobile" aria-pressed="false"><i class="fas fa-mobile-screen" aria-hidden="true"></i> Mobile</button>
        </div>
        <button type="button" id="theme-preview-close" aria-label="Close preview"><i class="fas fa-xmark" aria-hidden="true"></i></button>
    </div>
    <div class="theme-preview-stage"><iframe id="theme-preview-frame" title="Live storefront theme preview" src="about:blank"></iframe></div>
</dialog>
<style>
.theme-settings {max-width:1450px;margin:0 auto;padding:1.5rem .5rem 3rem;color:#202924;}
.theme-settings-heading {display:flex;align-items:center;justify-content:space-between;gap:2rem;margin-bottom:1.75rem;}
.theme-settings-kicker {font-size:.65rem;font-weight:800;letter-spacing:.15em;color:#778578;}
.theme-settings h1 {font-size:clamp(1.75rem,2.6vw,2.65rem);font-weight:750;letter-spacing:-.04em;margin:.65rem 0;}
.theme-settings-heading p {font-size:.9rem;line-height:1.7;color:#68736c;max-width:600px;margin:0;}
.theme-settings-heading>a {flex-shrink:0;font-size:.8rem;}
.theme-settings-note {display:flex;align-items:center;gap:.8rem;border:1px solid #e0e6df;background:#f3f7f1;border-radius:12px;padding:1rem 1.2rem;margin-bottom:1.75rem;font-size:.8rem;color:#53654c;}
.theme-selection-grid {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.5rem;}
.theme-choice-card {border:1px solid #dfe4df;border-radius:16px;background:white;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s;}
.theme-choice-card:hover {box-shadow:0 10px 28px #172c2010;}
.theme-choice-card.is-active {border:2px solid var(--choice-tone);}
.theme-choice-art {background:var(--choice-bg);position:relative;padding:1.25rem 1.25rem 0;}
.theme-choice-art img {display:block;width:100%;height:auto;aspect-ratio:8/5;object-fit:contain;border-radius:8px 8px 0 0;}
.theme-active-badge {position:absolute;top:.75rem;right:.75rem;border-radius:100px;background:var(--choice-tone);color:#fff;padding:.35rem .65rem;font-size:.65rem;font-weight:700;}
.theme-choice-content {padding:1.35rem;flex:1;display:flex;flex-direction:column;}
.theme-choice-version {text-transform:uppercase;letter-spacing:.09em;font-size:.6rem;font-weight:800;color:var(--choice-tone);}
.theme-choice-content h2 {font-size:1.2rem;font-weight:750;margin:.45rem 0 .75rem;letter-spacing:-.02em;}
.theme-choice-content p {font-size:.8rem;line-height:1.7;color:#748078;margin:0 0 1.25rem;flex:1;}
.theme-choice-actions {display:grid;grid-template-columns:1fr 1fr;gap:.6rem;}
.theme-choice-actions form {margin:0;}
.theme-preview-button,.theme-activate-button {width:100%;display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.75rem .5rem;border-radius:8px;font-size:.73rem;font-weight:700;cursor:pointer;border:1px solid #dfe4df;background:#fff;color:#38453c;}
.theme-activate-button {background:var(--choice-tone);border-color:var(--choice-tone);color:#fff;}
.theme-activate-button:disabled {background:#f0f3ef;color:#748078;border-color:#e5e9e3;cursor:default;}
.theme-preview-button:hover {background:var(--choice-bg);}
.theme-preview-dialog {padding:0;border:0;border-radius:16px;width:min(1280px,95vw);max-width:95vw;height:90vh;max-height:90vh;margin:auto;box-shadow:0 30px 100px #0005;}
.theme-preview-dialog::backdrop {background:#121e19bb;backdrop-filter:blur(5px);}
.theme-preview-toolbar {display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.25rem;border-bottom:1px solid #e1e6df;background:white;}
.theme-preview-toolbar strong {display:block;font-size:.9rem;}
.theme-preview-toolbar span {display:block;font-size:.7rem;color:#748078;margin-top:.2rem;}
.theme-preview-toolbar button {border:1px solid #e1e6df;border-radius:7px;background:white;padding:.5rem .8rem;cursor:pointer;font-size:.75rem;}
.theme-preview-devices {display:flex;gap:.4rem;}
.theme-preview-devices button[aria-pressed=true] {background:#273c2f;color:white;}
.theme-preview-stage {height:calc(100% - 79px);padding:1rem;display:flex;justify-content:center;background:#e9ede7;}
#theme-preview-frame {width:100%;height:100%;border:0;background:#fff;border-radius:8px;}
.theme-preview-dialog[data-device=mobile] #theme-preview-frame {width:390px;max-width:100%;border:7px solid #273c2f;border-radius:24px;}
@media(max-width:1100px){.theme-selection-grid {grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:650px){.theme-selection-grid {grid-template-columns:1fr;}.theme-settings-heading {align-items:flex-start;flex-direction:column;gap:1rem;}.theme-preview-toolbar {flex-wrap:wrap;padding:.75rem;}.theme-preview-toolbar>div:first-child {width:calc(100% - 50px);}.theme-preview-toolbar>button {position:absolute;right:.75rem;top:.75rem;}.theme-preview-stage {height:calc(100% - 116px);padding:.5rem;}}
</style>
@endsection
@push('scripts')
<script>
(() => {
    const dialog = document.getElementById('theme-preview-dialog');
    const frame = document.getElementById('theme-preview-frame');
    document.querySelectorAll('[data-preview-url]').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('theme-preview-title').textContent = button.dataset.themeName;
            frame.title = button.dataset.themeName + ' storefront preview';
            frame.src = button.dataset.previewUrl;
            dialog.showModal();
        });
    });
    document.querySelectorAll('[data-device]').forEach(button => {
        button.addEventListener('click', () => {
            dialog.dataset.device = button.dataset.device;
            document.querySelectorAll('button[data-device]').forEach(item => item.setAttribute('aria-pressed', String(item === button)));
        });
    });
    document.getElementById('theme-preview-close').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => { frame.src = 'about:blank'; });
    dialog.addEventListener('click', event => { if (event.target === dialog) {
        const bounds = dialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
    }});
})();
</script>
@endpush
