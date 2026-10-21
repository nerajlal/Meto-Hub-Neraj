<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\StorefrontSeo;

class SearchController extends Controller
{
    private function hostTenant(): ?Tenant
    {
        $tenant = app(\App\Services\IntegrationDomainVerification::class)->isPlatformHost(request()->getHost()) ? null : Tenant::where('domain', request()->getHost())->first();
        if ($tenant) {
            abort_unless(app(\App\Services\PlanAccess::class)->storefrontDomainAvailable($tenant), 403);
        }

        return $tenant;
    }

    private function tenants(?string $slug)
    {
        $host = $this->hostTenant();
        if ($slug) {
            $tenant = Tenant::where('slug', $slug)->firstOrFail();
            abort_if($host && $host->id !== $tenant->id, 404);

            return collect([$tenant]);
        }

        return $host ? collect([$host]) : Tenant::orderBy('id')->cursor();
    }

    private function xml(callable $writer)
    {
        return response()->stream(function () use ($writer) {
            echo '<?xml version="1.0" encoding="UTF-8"?>';
            $writer();
        }, 200, ['Content-Type' => 'application/xml; charset=UTF-8', 'X-Robots-Tag' => 'noindex']);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    public function robots(StorefrontSeo $seo)
    {
        $tenant = $this->hostTenant();
        $allow = (bool) config('search.indexable');
        $text = "User-agent: *\n".($allow ? "Allow: /\n" : "Disallow: /\n");
        if ($allow && (! $tenant || $seo->indexable($tenant))) {
            $base = $tenant ? request()->getSchemeAndHttpHost() : rtrim(config('app.url'), '/');
            $text .= 'Sitemap: '.$base."/sitemap.xml\n";
        }

        return response($text, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    public function index(StorefrontSeo $seo, ?string $store_slug = null)
    {
        $tenants = $this->tenants($store_slug);

        return $this->xml(function () use ($seo, $tenants) {
            echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            if (! request()->route('store_slug') && ! $this->hostTenant() && config('search.indexable')) {
                echo '<sitemap><loc>'.$this->escape(rtrim(config('app.url'), '/').'/sitemap-platform.xml').'</loc></sitemap>';
                if (config('services.blog.key')) {
                    echo '<sitemap><loc>'.$this->escape(rtrim(config('app.url'), '/').'/sitemap-blogs.xml').'</loc></sitemap>';
                }
            }
            foreach ($tenants as $tenant) {
                if (! $seo->indexable($tenant)) {
                    continue;
                }
                foreach (['pages' => 1, 'products' => (int) ceil(Product::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('status', 'active')->count() / 2000),
                    'collections' => (int) ceil(Collection::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('status', true)->count() / 2000)] as $type => $pages) {
                    for ($page = 1; $page <= $pages; $page++) {
                        echo '<sitemap><loc>'.$this->escape($seo->url($tenant, 'sitemap/'.$type.'/'.$page.'.xml')).'</loc></sitemap>';
                    }
                }
            }
            echo '</sitemapindex>';
        });
    }

    public function llms(\App\Services\MarketingContent $content)
    {
        abort_if($this->hostTenant() || ! config('search.indexable'), 404);

        return response($content->llms(), 200, ['Content-Type' => 'text/plain; charset=UTF-8',
            'X-Robots-Tag' => 'noindex, follow', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function platform(StorefrontSeo $seo)
    {
        abort_if($this->hostTenant() || ! config('search.indexable'), 404);

        return $this->xml(function () {
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach (['', '/features', '/pricing', '/themes', '/about', '/contact', '/blogs', '/privacy-policy', '/terms', '/refund-cancellation', '/security'] as $path) {
                echo '<url><loc>'.$this->escape(rtrim(config('app.url'), '/').$path).'</loc></url>';
            }
            echo '</urlset>';
        });
    }

    public function blogs(\App\Services\BlogFeed $feed)
    {
        abort_if($this->hostTenant() || ! config('search.indexable'), 404);
        $result = $feed->load();
        if ($result['state'] === 'unavailable') {
            return response('Blog sitemap temporarily unavailable. Please retry later.', 503,
                ['Content-Type' => 'text/plain; charset=UTF-8', 'Retry-After' => '3600']);
        }
        $blogs = collect($result['blogs'])->unique('id');

        return $this->xml(function () use ($blogs) {
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($blogs as $blog) {
                $url = rtrim(config('app.url'), '/').'/blogs/'.$blog['id'];
                echo '<url><loc>'.$this->escape($url).'</loc>';
                if (! empty($blog['modified_at']) && $blog['modified_at'] <= now()->timestamp) {
                    echo '<lastmod>'.gmdate('Y-m-d\TH:i:s\Z', $blog['modified_at']).'</lastmod>';
                }
                echo '</url>';
            }
            echo '</urlset>';
        });
    }

    public function part(StorefrontSeo $seo, string $store_slug, string $type, int $page)
    {
        $tenant = $this->tenants($store_slug)->first();
        abort_unless($seo->indexable($tenant) && $page >= 1 && in_array($type, ['pages', 'products', 'collections']), 404);
        if ($type === 'pages') {
            abort_unless($page === 1, 404);
            $urls = collect(['home', 'all-products', 'about', 'contact'])->map(fn ($name) => $seo->url($tenant, $name));
        } else {
            $model = $type === 'products' ? Product::class : Collection::class;
            $items = $model::withoutGlobalScopes()->where('tenant_id', $tenant->id)->where('status', $type === 'products' ? 'active' : true)->orderBy('id')->offset(($page - 1) * 2000)->limit(2000)->get();
            abort_if($items->isEmpty(), 404);
            $urls = $items->map(fn ($item) => $seo->url($tenant, $type === 'products' ? 'product' : 'collections', [$type === 'products' ? 'id' : 'slug' => $type === 'products' ? $item->id : $item->slug]));
        }

        return $this->xml(function () use ($urls) {
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            foreach ($urls as $url) {
                echo '<url><loc>'.$this->escape($url).'</loc></url>';
            }
            echo '</urlset>';
        });
    }
}
