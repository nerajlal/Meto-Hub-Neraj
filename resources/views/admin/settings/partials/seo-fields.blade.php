<h2 class="h5">Search and social appearance</h2>
<p class="small text-muted">Optional. Leave blank to use your store or catalog content.</p>
<label for="seo_title" class="form-label">Search title</label>
<input class="form-control mb-3" id="seo_title" name="seo_title" maxlength="120" value="{{ old('seo_title', $entity->seo_title ?? '') }}">
<label for="seo_description" class="form-label">Search description</label>
<textarea class="form-control mb-3" id="seo_description" name="seo_description" maxlength="320" rows="3">{{ old('seo_description', $entity->seo_description ?? '') }}</textarea>
<label for="seo_image" class="form-label">Social image URL</label>
<input class="form-control" id="seo_image" name="seo_image" type="url" maxlength="2048" placeholder="https://…" value="{{ old('seo_image', $entity->seo_image ?? '') }}">
<p class="small text-muted mt-2">Use a public HTTPS image. The product image, collection image or store logo is used when blank.</p>
