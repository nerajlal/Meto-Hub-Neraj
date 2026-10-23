<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreIntegration;
use App\Models\Tenant;
use App\Services\PlanAccess;
use App\Services\StorefrontTheme;
use Illuminate\Http\Request;

class SettingsOverviewController extends Controller
{
    public function index(Request $request, PlanAccess $access, StorefrontTheme $themes)
    {
        $tenant = Tenant::findOrFail($request->attributes->get('resolved_tenant_id'));
        $subscription = $tenant->razorpay_subscription_id
            ? $tenant->subscriptions()->where('gateway_id', $tenant->razorpay_subscription_id)->first()
            : $tenant->subscriptions()->latest('id')->first();
        $paid = $access->hasPaidAccess($tenant);
        $trial = $access->inTrial($tenant);
        $trialEnds = $tenant->created_at->copy()->addDays(config('saas.trial_days'));
        $pending = in_array($subscription?->status ?? $tenant->subscription_status, ['creating', 'created', 'authenticated', 'pending'], true);
        $status = $paid ? 'Paid access active' : ($trial ? 'Free trial active' : ($pending ? 'Payment pending' : 'Subscription required'));
        $statusTone = $paid ? 'success' : ($trial ? 'trial' : 'attention');
        $planKey = ['sprout' => 'base', 'blossom' => 'pro', 'tree' => 'enterprise', 'maison' => 'pro', 'heritage' => 'enterprise'][$tenant->plan] ?? $tenant->plan;
        $planName = config('saas.plans.'.$planKey.'.name') ?: 'Plan not selected';
        $activeTheme = $themes->all()[$themes->normalize($tenant->theme)]['name'];
        $integration = StoreIntegration::where('tenant_id', $tenant->id)->first();
        $row = fn ($label, $description, $icon, $route, $badge = null) => compact('label', 'description', 'icon', 'route', 'badge');
        $groups = [
            'Store setup' => [
                $row('Store details & branding', 'Store name, logo, colors and contact details', 'store', 'admin.settings.storefront'),
                $row('Storefront themes', 'Choose your store’s appearance', 'palette', 'admin.settings.theme', $activeTheme),
                $row('Payment methods', 'Customer cash-on-delivery settings', 'credit-card', 'admin.settings.payment'),
                $row('Checkout fields', 'Choose the details customers enter at checkout', 'list-check', 'admin.settings.checkout-fields'),
                $row('Discounts & coupons', 'Manage your store’s offers', 'tags', 'admin.discounts'),
                $row('Collections', 'Organize products into collections', 'layer-group', 'admin.collections'),
            ],
            'Delivery' => [
                $row('Delivery partners', 'Manage partner records and tracking details', 'truck', 'admin.settings.delivery-partners.index'),
            ],
            'Homepage content' => [
                $row('Hero slider', 'Update your storefront banners', 'images', 'admin.settings.slider'),
                $row('Instagram reels', 'Showcase your store’s reels', 'film', 'admin.settings.reels'),
                $row('Home products', 'Choose products to feature on your homepage', 'box', 'admin.settings.home-products'),
            ],
            'Search & marketing' => [
                $row('Search readiness', 'Page titles, descriptions and indexing preferences', 'magnifying-glass', 'admin.settings.seo'),
            ],
        ];
        foreach (['ga4' => ['Google Analytics 4', 'ga4_id', 'chart-line'], 'gtm' => ['Google Tag Manager', 'gtm_id', 'code'],
            'gsc' => ['Google Search Console', 'gsc_token', 'magnifying-glass'], 'meta' => ['Meta Pixel / Ads', 'meta_pixel_id', 'bullhorn']] as $provider => [$label, $field, $icon]) {
            $configured = filled($integration?->getAttribute($field));
            $badge = ! $configured ? 'Not configured' : ($integration->getAttribute($provider.'_enabled') ? 'Configured' : 'Disabled');
            if ($configured && $integration->getAttribute($provider.'_enabled')) {
                if (in_array($provider, ['ga4', 'meta']) && $integration->mode === 'gtm') {
                    $badge = 'Paused in GTM mode';
                } elseif ($provider === 'gtm' && $integration->mode !== 'gtm') {
                    $badge = 'Paused in direct mode';
                }
            }
            $groups['Search & marketing'][] = $row($label, 'Manage setup in Integrations', $icon, 'admin.settings.integrations', $badge) + ['fragment' => 'integration-'.$provider];
        }
        $groups['Notifications & support'] = [
            $row('Order notifications', 'Your notification inbox and device alert setup', 'bell', 'admin.notifications.index'),
            $row('Contact support', 'Get help with your MetoHub store', 'headset', 'landing.contact'),
        ];
        $groups['Account'] = [
            [
                'label' => 'Store Maintenance',
                'description' => 'Show a maintenance page to customers',
                'icon' => 'store',
                'url' => '#store-maintenance',
                'danger' => true,
                'badge' => $tenant->maintenance_mode ? 'Active' : null,
                'modal' => 'maintenanceModal',
            ],
            [
                'label' => 'Delete Site',
                'description' => 'Permanently delete this site',
                'icon' => 'trash-can',
                'url' => '#delete-site',
                'danger' => true,
                'modal' => 'deleteSiteModal',
            ],
            [
                'label' => 'Account Deletion',
                'description' => 'Request account deletion',
                'icon' => 'user-xmark',
                'url' => '#account-deletion',
                'danger' => true,
                'badge' => $tenant->deletion_requested_at ? 'Pending Review' : null,
                'modal' => 'accountDeletionModal',
            ],
        ];

        return view('admin.settings.index', compact('tenant', 'subscription', 'paid', 'trial', 'trialEnds', 'status', 'statusTone', 'planName', 'groups', 'access'));
    }
}
