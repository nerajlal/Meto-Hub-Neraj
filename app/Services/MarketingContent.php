<?php

namespace App\Services;

class MarketingContent
{
    public function summary(): string
    {
        return 'MetoHub helps Instagram sellers and independent brands create an online store with '.count(config('storefront_themes')).' customizable themes, product variants and order management. Plans start at ₹'.number_format(config('saas.plans.base.monthly_price')).'/month, with a '.config('saas.trial_days').'-day free trial and no credit card required. Customer checkout currently supports cash on delivery.';
    }

    public function faqs(): array
    {
        $price = fn ($plan) => '₹'.number_format(config("saas.plans.$plan.monthly_price"));

        return [
            ['question' => 'Is it really '.$price('base').' per month?', 'answer' => 'Yes. The Base plan is '.$price('base').'/month after your '.config('saas.trial_days').'-day free trial. Pro is '.$price('pro').'/month and Enterprise '.$price('enterprise').'/month.'],
            ['question' => 'Do I need coding skills?', 'answer' => 'No. You pick a theme, add your details and products from a simple dashboard.'],
            ['question' => 'Can I sell products other than clothing?', 'answer' => 'Yes — grocery, gadgets, jewelry, watches, perfume and other catalog-based businesses all work well.'],
            ['question' => 'Can I personalize the theme and store content?', 'answer' => 'Yes. Change your theme, logo, colors and homepage content whenever you like.'],
            ['question' => 'How do customers pay for orders?', 'answer' => 'Customer checkout currently supports cash on delivery. Razorpay handles your MetoHub subscription. Recording an order as paid does not charge or transfer money.'],
            ['question' => 'Can I use my own domain?', 'answer' => 'Connecting your own domain is available on Pro and Enterprise. Buying the domain itself is separate.'],
            ['question' => 'What happens after the free trial?', 'answer' => 'After the trial, your dashboard is limited to Billing until a paid subscription is confirmed. Your storefront stays open for '.config('saas.storefront_grace_days').' days after access expires, then shows an unavailable message. Your records are retained.'],
            ['question' => 'What do I need to create a store?', 'answer' => 'Your name, email, mobile number, country, business name and a password. Choose a theme during setup, then add your products.'],
        ];
    }

    public function llms(): string
    {
        $base = rtrim(config('app.url'), '/');
        $text = "# MetoHub\n\n> ".$this->summary()."\n\n";
        $text .= 'Subscriptions are priced in INR and billed monthly. ';
        foreach (config('saas.plans') as $plan) {
            $text .= $plan['name'].': ₹'.number_format($plan['monthly_price']).'/month. ';
        }
        $text .= "Razorpay processes platform subscriptions, separately from customer orders. Custom domains are available on Pro and Enterprise; domain purchase is separate.\n\n";
        $text .= 'The '.config('saas.trial_days').'-day free trial is separate from the '.config('marketing.policies.refund_days')."-day first paid subscription refund request window; see the refund policy for conditions.\n\n";
        $text .= 'Contact: '.config('marketing.contact.email').', '.config('marketing.contact.phone').', '.config('marketing.contact.location').".\n\n## Platform\n\n";
        foreach (['' => 'Home', '/features' => 'Features', '/pricing' => 'Pricing', '/themes' => 'Themes', '/about' => 'About', '/contact' => 'Contact', '/blogs' => 'Blog', '/sitemap.xml' => 'Sitemap index'] as $path => $label) {
            $text .= "- [$label]($base$path)\n";
        }
        $text .= "\n## Policies\n\n";
        foreach (['/privacy-policy' => 'Privacy policy', '/terms' => 'Terms of service', '/refund-cancellation' => 'Refund and cancellation', '/security' => 'Security'] as $path => $label) {
            $text .= "- [$label]($base$path)\n";
        }

        return $text;
    }
}
