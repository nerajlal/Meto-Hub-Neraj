<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Support\Str;

class StorefrontSeo
{
    public function indexable(Tenant $tenant): bool
    {
        return (bool) config('search.indexable') && $tenant->search_indexable !== false;
    }

    public function url(Tenant $tenant, string $page = 'home', array $query = []): string
    {
        // Use the current custom host only when it is the resolved tenant host.
        // Do not publish a configured but unverified domain as a canonical.
        $custom = $tenant->domain && request()->getHost() === $tenant->domain;
        $base = $custom ? request()->getSchemeAndHttpHost() : rtrim(config('app.url'), '/');
        $path = $custom && $page === 'home' ? '' : '/store/'.rawurlencode($tenant->slug).($page === 'home' ? '/' : '/'.$page);

        return $base.$path.($query ? '?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '');
    }

    public function image(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (Str::startsWith($path, ['https://', 'http://'])) {
            return $path;
        }

        return url(app(StorefrontProduct::class)->imageUrl($path));
    }

    public function data(Tenant $tenant, array $view): array
    {
        $page = Str::afterLast(request()->route()?->getName() ?? '', '.');
        $page = in_array($page, ['collection', 'products']) ? 'collections' : $page;
        if (request()->path() === '/' && $tenant->domain === request()->getHost()) {
            $page = 'home';
        }
        // Child Blade loops can leave product/collection variables behind on home pages.
        $product = $page === 'product' && ($view['product'] ?? null) instanceof Product ? $view['product'] : null;
        $collection = $page === 'collections' && ($view['collection'] ?? null) instanceof Collection ? $view['collection'] : null;
        $query = [];
        if ($product) {
            $query['id'] = $product->id;
        } elseif ($page === 'combo' && request()->filled('id')) {
            $query['id'] = request()->query('id');
        } elseif ($collection) {
            $query['slug'] = $collection->slug;
        }
        if (request()->integer('page') > 1) {
            $query['page'] = request()->integer('page');
        }
        $canonical = $this->url($tenant, $page, $query);
        $entity = $product ?? $collection;
        $label = $product?->title ?? $collection?->name ?? ($page === 'home' ? null : Str::headline($page));
        $title = $entity?->seo_title ?: ($label ? $label.' | '.$tenant->name : ($tenant->seo_title ?: $tenant->name));
        $description = $entity?->seo_description ?: ($entity?->description ?: ($tenant->seo_description ?: 'Explore products and collections from '.$tenant->name.'.'));
        $description = Str::limit(trim(preg_replace('/\s+/u', ' ', strip_tags($description))), 320, '');
        $image = $entity?->seo_image ?: ($product?->images->first()?->path ?? $collection?->image ?? $tenant->seo_image ?? $tenant->logo);
        $safePages = ['home', 'product', 'collections', 'all-products', 'about', 'contact', 'shipping-policy', 'return-policy', 'terms-of-service', 'combos', 'combo'];
        $filtered = count(array_diff(array_keys(request()->query()), ['id', 'slug', 'page', 'store', 'tenant', 'tenant_id', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'])) > 0;
        $noindex = ! $this->indexable($tenant) || ! in_array($page, $safePages) || $filtered || ($page === 'product' && ! $product);
        $graph = [['@type' => 'Organization', '@id' => $this->url($tenant).'#organization', 'name' => $tenant->name, 'url' => $this->url($tenant)]];
        if ($product) {
            $details = app(StorefrontProduct::class)->data($product);
            $variants = $details['variants'];
            $schema = ['@type' => 'Product', 'name' => $product->title, 'description' => $description, 'url' => $canonical];
            if ($image) {
                $schema['image'] = [$this->image($image)];
            }
            if ($variants->isNotEmpty()) {
                $schema['offers'] = ['@type' => 'AggregateOffer', 'url' => $canonical,
                    'priceCurrency' => $tenant->getRawOriginal('currency') ?: 'INR', 'lowPrice' => $variants->min('price'),
                    'highPrice' => $variants->max('price'), 'offerCount' => $variants->count(),
                    'availability' => 'https://schema.org/'.($variants->contains('available', true) ? 'InStock' : 'OutOfStock')];
            }
            $graph[] = $schema;
        }
        if ($page !== 'home') {
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => $tenant->name, 'item' => $this->url($tenant)],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $label ?: Str::headline($page), 'item' => $canonical],
            ]];
        }

        return compact('title', 'description', 'canonical', 'noindex', 'graph') + ['image' => $this->image($image), 'type' => $product ? 'product' : 'website'];
    }
}
