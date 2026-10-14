<input type="hidden" name="store" value="{{ $currentTenant->slug }}">
@if(request()->query('preview') == 1 && request()->filled('preview_theme'))
    <input type="hidden" name="preview" value="1">
    <input type="hidden" name="preview_theme" value="{{ $storefrontTheme['id'] }}">
@endif
