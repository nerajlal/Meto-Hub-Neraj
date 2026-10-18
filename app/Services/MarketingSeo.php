<?php

namespace App\Services;

use Illuminate\Http\Request;

class MarketingSeo
{
    public function pages(): array
    {
        return [
            '/' => ['Online Store Builder for Instagram Sellers | MetoHub', 'Create a branded online store for your Instagram business with themes, product variants and order management. Plans from ₹'.config('saas.plans.base.monthly_price').'/month. Try free for '.config('saas.trial_days').' days.'],
            '/admin/login' => ['Merchant Login | MetoHub', 'Sign in to your MetoHub merchant dashboard to manage your store, products and orders.'],
            '/features' => ['Online Store Features for Small Brands | MetoHub', 'Explore storefront themes, products, collections, discounts and order tools for your small business with MetoHub.'],
            '/pricing' => ['Online Store Pricing & Free Trial | MetoHub', 'Compare Base, Pro and Enterprise. Plans start at ₹'.config('saas.plans.base.monthly_price').'/month with a '.config('saas.trial_days').'-day free trial. Connect your own domain on Pro.'],
            '/themes' => ['Online Store Themes for Your Brand | MetoHub', 'Explore customizable storefront themes for clothing, jewelry, grocery, gadgets and more. Build a store that feels like your brand.'],
            '/about' => ['About MetoHub | Online Stores for Independent Brands', 'Learn about MetoHub, the platform helping independent sellers create and manage their own branded online stores.'],
            '/contact' => ['Contact MetoHub | Store Setup & Support', 'Contact MetoHub at '.config('marketing.contact.email').' or '.config('marketing.contact.phone').'. Based in '.config('marketing.contact.location').'.'],
            '/blogs' => ['MetoHub Blog — Grocery and Ecommerce Insights', 'Read updates and practical insights about ecommerce, independent selling and running your online store with MetoHub.'],
            '/privacy-policy' => ['Privacy Policy | MetoHub', 'Read how MetoHub handles personal information, uses account data and provides privacy contact information.'],
            '/terms' => ['Terms of Service | MetoHub', 'Read the terms governing MetoHub accounts, subscriptions and use of the online store platform.'],
            '/refund-cancellation' => ['Refund & Cancellation Policy | MetoHub', 'Review MetoHub subscription refund and cancellation terms, and find contact details for assistance.'],
            '/security' => ['Security at MetoHub', 'Read about MetoHub security practices, account protection and how to contact the team about a security concern.'],
            '/solutions' => ['Online Store Solutions for Small Businesses | MetoHub', 'Discover how MetoHub helps independent businesses organize their products, build storefronts and manage orders.'],
            '/analytics' => ['Store Analytics & Reporting | MetoHub', 'Explore MetoHub store reporting tools and learn about the analytics options available for your business.'],
            '/careers' => ['Careers at MetoHub', 'Explore working with MetoHub and find contact information for career enquiries.'],
            '/press' => ['MetoHub Press & Media', 'Find MetoHub company information and contact the team for press and media enquiries.'],
            '/independent-grocers' => ['Online Stores for Independent Grocers | MetoHub', 'Explore storefront and catalog tools for independent grocery businesses with MetoHub.'],
            '/grocery-chains' => ['Grocery Business Storefronts | MetoHub', 'Learn about MetoHub online storefront and catalog solutions for grocery businesses.'],
            '/farmers-markets' => ['Online Stores for Fresh Food Sellers | MetoHub', 'Discover online catalog and storefront tools for fresh food sellers and farmers market businesses.'],
            '/help-center' => ['MetoHub Help Center', 'Find guidance for creating your MetoHub store, managing products and understanding your account.'],
            '/api-docs' => ['MetoHub Developer Information', 'Explore MetoHub developer information and contact the team about integration requirements.'],
            '/guides' => ['Online Store Guides | MetoHub', 'Explore guides and information for setting up and managing an independent online store.'],
        ];
    }

    public function metadata(Request $request, array $overrides = []): array
    {
        $base = rtrim(config('app.url'), '/');
        $path = '/'.ltrim($request->path(), '/');
        $page = $this->pages()[$path] ?? $this->pages()['/'];
        $title = ($overrides['title'] ?? '') ?: $page[0];
        $description = ($overrides['description'] ?? '') ?: $page[1];
        $image = $overrides['image'] ?? '';
        if (! filter_var($image, FILTER_VALIDATE_URL) || ! in_array(parse_url($image, PHP_URL_SCHEME), ['http', 'https'])) {
            $image = $base.'/images/landing/social-card.png';
        }
        $url = $base.($path === '/' ? '' : $path);
        $organization = ['@type' => 'Organization', '@id' => $base.'/#organization', 'name' => 'MetoHub', 'url' => $base,
            'logo' => $base.'/merchant-icon-512.png', 'email' => config('marketing.contact.email'), 'telephone' => config('marketing.contact.phone_uri'),
            'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Infopark', 'addressLocality' => 'Kochi', 'addressRegion' => 'Kerala', 'addressCountry' => 'IN']];
        $schema = ['@context' => 'https://schema.org', '@graph' => [$organization,
            ['@type' => 'WebSite', '@id' => $base.'/#website', 'url' => $base, 'name' => 'MetoHub', 'publisher' => ['@id' => $base.'/#organization']],
            ['@type' => 'WebPage', '@id' => $url.'#webpage', 'url' => $url, 'name' => $title, 'description' => $description, 'isPartOf' => ['@id' => $base.'/#website']]]];

        if (in_array($path, ['/', '/pricing'], true)) {
            $schema['@graph'][] = ['@type' => 'SoftwareApplication', '@id' => $base.'/#application',
                'name' => 'MetoHub', 'url' => $base, 'applicationCategory' => 'BusinessApplication', 'operatingSystem' => 'Web',
                'description' => app(MarketingContent::class)->summary(), 'publisher' => ['@id' => $base.'/#organization'],
                'offers' => collect(config('saas.plans'))->map(fn ($plan) => [
                    '@type' => 'Offer', 'name' => 'MetoHub '.$plan['name'].' monthly subscription', 'url' => $base.'/#pricing',
                    'price' => number_format($plan['monthly_price'], 2, '.', ''), 'priceCurrency' => config('saas.currency'),
                    'priceSpecification' => ['@type' => 'UnitPriceSpecification', 'price' => number_format($plan['monthly_price'], 2, '.', ''),
                        'priceCurrency' => config('saas.currency'), 'unitText' => 'month', 'billingDuration' => 'P1M'],
                ])->values()->all()];
        }
        if ($path === '/') {
            $schema['@graph'][] = ['@type' => 'FAQPage', '@id' => $base.'/#faq', 'mainEntity' =>
                array_map(fn ($faq) => ['@type' => 'Question', 'name' => $faq['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']]], app(MarketingContent::class)->faqs())];
        }

        $posting = null;
        $article = $overrides['article'] ?? null;
        if ($request->routeIs('landing.blog.show') && is_array($article)) {
            $posting = ['@type' => 'BlogPosting', '@id' => $url.'#article', 'url' => $url,
                'headline' => $article['title'], 'description' => $article['seo_description'] ?? $article['excerpt'], 'image' => [$image],
                'mainEntityOfPage' => ['@id' => $url.'#webpage'], 'publisher' => ['@id' => $base.'/#organization']];
            if ($article['author_name'] === 'MetoHub team') {
                $posting['author'] = ['@id' => $base.'/#organization'];
            } else {
                $posting['author'] = ['@type' => 'Person', 'name' => $article['author_name']];
            }
            foreach (['published_at' => 'datePublished', 'modified_at' => 'dateModified'] as $field => $property) {
                $timestamp = $article[$field] ?? 0;
                if (is_numeric($timestamp) && $timestamp > 0 && $timestamp <= now()->timestamp
                    && ($field !== 'modified_at' || $timestamp >= ($article['published_at'] ?? 0))) {
                    $posting[$property] = gmdate('Y-m-d\\TH:i:s\\Z', (int) $timestamp);
                }
            }
            $schema['@graph'][] = $posting;
            $schema['@graph'][] = ['@type' => 'BreadcrumbList', 'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $base],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => $base.'/blogs'],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $article['title'], 'item' => $url],
            ]];
        }

        return compact('title', 'description', 'image', 'url', 'schema') + [
            'article' => $posting,
            'robots' => config('search.indexable') && ! $request->is('admin', 'admin/*', 'superadmin', 'superadmin/*', 'saas/*') && ! $request->has('preview') && ! $request->has('get_started')
                ? 'index, follow, max-image-preview:large' : 'noindex, nofollow',
            'type' => ($overrides['type'] ?? '') === 'article' ? 'article' : 'website',
        ];
    }
}
