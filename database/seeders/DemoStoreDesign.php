<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Slider;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class DemoStoreDesign
{
    public static function profiles(): array
    {
        return [
            'mens-clothing' => ['template_7', '#843d32', '#302923', '#f5e7df', 'THE MODERN WARDROBE', 'Good clothes. Better everyday.', 'Clean shirts, relaxed trousers and reliable essentials. Build a wardrobe that works as hard as you do.', 'Find your fit', 'TF'],
            'grocery' => ['template_2', '#21633b', '#153b26', '#e8f3df', 'FRESH PICKS, EVERY DAY', 'From the farm to your family.', 'Seasonal vegetables, crisp fruit and pantry favorites, all in one easy basket.', 'Shop fresh', 'FB'],
            'gadgets' => ['template_8', '#245ce0', '#122246', '#e7efff', 'UPGRADE YOUR EVERYDAY', 'Small tech. Big possibilities.', 'Discover audio, charging essentials and smart accessories that make every day run better.', 'Explore the tech', 'CC'],
            'jewelry' => ['template_13', '#80602c', '#30271c', '#f5ebd7', 'A LITTLE EVERYDAY BRILLIANCE', 'Pieces to keep close.', 'Delicate pearls, sculptural hoops and effortless bracelets. Find the piece that feels like you.', 'Discover the jewels', 'AJ'],
            'watches' => ['template_11', '#252525', '#151515', '#eeeeeb', 'PRECISION / CHARACTER / TIME', 'Make every hour your own.', 'Classic leather, modern mesh and purposeful chronographs. A considered collection for every wrist.', 'Find your watch', 'HH'],
            'womens-clothing' => ['template_12', '#97556f', '#462d3a', '#fae9ef', 'SOFT SHAPES. STRONG STYLE.', 'Dress for your kind of day.', 'Easy linen dresses, expressive prints and everyday layers. Discover clothes you will reach for again and again.', 'Explore the edit', 'LG'],
            'perfume' => ['template_9', '#b18b4c', '#211c29', '#f1e8d7', 'THE ART OF SCENT', 'Leave a little mystery.', 'Bright citrus, velvet florals and warm woods. Explore fragrances that tell your story long after you leave.', 'Find your signature', 'MM'],
        ];
    }

    public static function apply(Tenant $tenant): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Demo designs are local only.');
        }
        $profile = self::profiles()[$tenant->slug] ?? null;
        if (! $profile) {
            return;
        }
        [$theme,$primary,$dark,$accent,$eyebrow,$heading,$description,$button,$initials] = $profile;
        $disk = Storage::disk('public');
        $base = 'demo-branding/'.$tenant->slug;
        $logo = '<svg xmlns="http://www.w3.org/2000/svg" width="180" height="180"><rect width="180" height="180" rx="'.($theme === 'template_12' ? 90 : 8).'" fill="'.$dark.'"/><rect x="12" y="12" width="156" height="156" rx="4" fill="none" stroke="'.$accent.'" opacity=".4"/><text x="90" y="111" text-anchor="middle" font-family="Georgia,serif" font-size="58" fill="'.$accent.'">'.$initials.'</text></svg>';
        $disk->put($base.'-logo.svg', $logo);
        // Category-specific local catalog illustrations make the demo entirely self contained.
        $products = Product::withoutGlobalScopes()->where('tenant_id', $tenant->id)->with('images')->get();
        foreach (['desktop', 'mobile'] as $format) {
            $w = $format === 'desktop' ? 1400 : 700;
            $h = 900;
            $background = $theme === 'template_9' ? $dark : $accent;
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'"><rect width="100%" height="100%" fill="'.$background.'"/>';
            $svg .= match ($tenant->slug) {
                'gadgets' => '<path d="M0 120H1400M0 330H1400M0 540H1400M0 750H1400M180 0V900M430 0V900M680 0V900M930 0V900M1180 0V900" stroke="'.$primary.'" opacity=".2" stroke-width="2"/>',
                'jewelry' => '<circle cx="700" cy="450" r="330" fill="none" stroke="'.$primary.'" stroke-width="3"/><circle cx="700" cy="450" r="380" fill="none" stroke="'.$primary.'" opacity=".3" stroke-width="2"/>',
                'watches' => '<path d="M0 0L1400 900M0 900L1400 0" stroke="'.$primary.'" opacity=".18" stroke-width="90"/>',
                'womens-clothing' => '<ellipse cx="400" cy="450" rx="480" ry="520" fill="'.$primary.'" opacity=".13"/>',
                'grocery' => '<circle cx="180" cy="180" r="240" fill="'.$primary.'" opacity=".12"/><circle cx="1200" cy="850" r="360" fill="'.$primary.'" opacity=".18"/>',
                'perfume' => '<ellipse cx="700" cy="440" rx="600" ry="350" fill="'.$primary.'" opacity=".2"/><path d="M100 0V900M1300 0V900" stroke="'.$primary.'" opacity=".5" stroke-width="2"/>',
                default => '<path d="M0 0H600L1400 900H800Z" fill="'.$primary.'" opacity=".12"/>',
            };
            foreach (($format === 'desktop' ? [0, 3, 6] : [0]) as $i => $index) {
                $path = $products[$index]->images->first()?->path;
                if (! $path || ! $disk->exists($path)) {
                    continue;
                }
                $image = base64_encode($disk->get($path));
                $x = $format === 'desktop' ? 80 + $i * 425 : 80;
                $y = $format === 'desktop' ? 140 + ($i === 1 ? 70 : 0) : 160;
                $svg .= '<image x="'.$x.'" y="'.$y.'" width="'.($format === 'desktop' ? 390 : 540).'" height="580" href="data:image/svg+xml;base64,'.$image.'"/>';
            }
            $svg .= '</svg>';
            $disk->put($base.'-'.$format.'.svg', $svg);
        }
        $tenant->forceFill(['theme' => $theme, 'primary_color' => $primary, 'dark_color' => $dark, 'accent_color' => $accent, 'logo' => $base.'-logo.svg', 'hero_eyebrow' => $eyebrow, 'hero_heading' => $heading, 'hero_description' => $description, 'hero_button_label' => $button])->save();
        Slider::withoutGlobalScopes()->updateOrCreate(['tenant_id' => $tenant->id, 'title' => 'Demo brand collection'], ['image_desktop' => $base.'-desktop.svg', 'image_mobile' => $base.'-mobile.svg', 'status' => true, 'order' => 0]);
    }
}
