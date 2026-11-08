<div>
    <label for="bundle-image" class="form-label fw-medium text-secondary small">Cover image</label>
    @if($currentImage ?? null)
        <div class="mb-2"><img src="{{ Storage::url($currentImage) }}" alt="Current bundle cover" class="rounded border" style="width:100px;height:100px;object-fit:cover"></div>
    @endif
    <input id="bundle-image" type="file" name="image" class="form-control" accept="image/webp,image/png,image/jpeg">
    <p class="form-text mb-0">JPG, PNG or WebP, up to 5 MB. Images automatically convert to WebP before saving. Leave empty to keep the current image.</p>
</div>
@include('admin.bundles.partials.image-conversion')
