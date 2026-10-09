<input type="hidden" name="tenant_id" value="{{ $currentTenant->id }}">
@if(request()->query('preview') == 1 && request()->filled('preview_theme'))
    <input type="hidden" name="preview" value="1">
    <input type="hidden" name="preview_theme" value="{{ $storefrontTheme['id'] }}">
@endif
