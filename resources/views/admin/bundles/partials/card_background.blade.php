@php
    $background = old('card_background', isset($bundle) ? ($bundle->card_background ?? []) : []) ?? [];
    $background = is_array($background) ? array_filter($background, fn ($value) => is_scalar($value)) : [];
    $backgroundMode = $background['mode'] ?? 'auto';
@endphp
<div class="card border shadow-sm p-3 mb-4" data-bundle-background>
    <h2 class="h6 fw-bold text-secondary mb-2">Promotional card background</h2>
    <p class="small text-muted">For the wide bundle and pack cards on the Pantry Select homepage.</p>
    <label for="bundle-background-mode" class="form-label small">Background style</label>
    <select id="bundle-background-mode" name="card_background[mode]" class="form-select mb-3">
        @foreach(['auto' => 'Theme default', 'solid' => 'Solid color', 'gradient' => 'Gradient'] as $value => $label)<option value="{{ $value }}" @selected($backgroundMode === $value)>{{ $label }}</option>@endforeach
    </select>
    <div data-background-colors @if($backgroundMode === 'auto') hidden @endif>
        @foreach(['color' => ['First color', '#fff0bd'], 'color_end' => ['Second color', '#dce9fa']] as $field => [$label, $default])
            <div class="mb-3" @if($field === 'color_end') data-background-gradient @if($backgroundMode !== 'gradient') hidden @endif @endif>
                <label for="bundle-background-{{ $field }}" class="form-label small">{{ $label }}</label>
                <div class="d-flex gap-2">
                    <input type="color" class="form-control form-control-color" data-color-picker="{{ $field }}" aria-label="Pick {{ strtolower($label) }}" value="{{ preg_match('/^#[a-f0-9]{6}$/i', $background[$field] ?? '') ? $background[$field] : $default }}">
                    <input type="text" class="form-control" id="bundle-background-{{ $field }}" name="card_background[{{ $field }}]" value="{{ $background[$field] ?? $default }}" pattern="#[a-fA-F0-9]{6}" maxlength="7" aria-label="{{ $label }} hex code">
                </div>
            </div>
        @endforeach
        <div data-background-gradient @if($backgroundMode !== 'gradient') hidden @endif>
            <label for="bundle-background-angle" class="form-label small">Gradient direction (degrees)</label>
            <input id="bundle-background-angle" type="number" name="card_background[angle]" class="form-control mb-3" min="0" max="360" step="1" value="{{ $background['angle'] ?? 135 }}">
        </div>
    </div>
    @foreach(['card_background', 'card_background.mode', 'card_background.color', 'card_background.color_end', 'card_background.angle'] as $field)@error($field)<div class="text-danger small mb-2">{{ $message }}</div>@enderror @endforeach
    <div data-background-preview class="rounded p-3" style="min-height:120px;background:#fff0bd;color:#000">
        <span class="small d-block mb-2">Card preview</span><strong class="d-block mb-3" data-background-title>{{ old('title', $bundle->title ?? 'Your bundle') }}</strong><span class="d-inline-block bg-white text-dark px-3 py-1 rounded small">Add +</span>
    </div>
</div>
<script src="{{ asset('js/admin-bundle-background.js').'?v='.filemtime(public_path('js/admin-bundle-background.js')) }}" defer></script>
