<?php
namespace App\Services;

class ThemeAvailability
{
    public function available(): array
    {
        $request = request();
        if (!$request->attributes->has('available_storefront_themes')) {
            $hidden = \App\Models\ThemeAvailability::where('is_available', false)->pluck('theme')->all();
            $request->attributes->set('available_storefront_themes', array_diff_key(config('storefront_themes'), array_flip($hidden)));
        }
        return $request->attributes->get('available_storefront_themes');
    }
    public function defaultTheme(): ?string
    {
        $available = $this->available();
        return isset($available['template_1']) ? 'template_1' : array_key_first($available);
    }
}
