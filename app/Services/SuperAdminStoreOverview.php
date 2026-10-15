<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;

class SuperAdminStoreOverview
{
    public const STATUSES = [
        'trial' => 'In trial', 'trial_expired' => 'Trial expired', 'pending' => 'Payment pending',
        'active' => 'Subscription active', 'halted' => 'Payment halted', 'paused' => 'Subscription paused',
        'cancelled' => 'Subscription cancelled', 'completed' => 'Subscription completed', 'expired' => 'Subscription expired', 'unknown' => 'Needs review',
    ];

    public function describe(Tenant $tenant): array
    {
        $raw = $tenant->subscription_status;
        $trialEnd = $tenant->created_at->copy()->addDays(config('saas.trial_days', 7));
        $active = $raw === 'active';
        $days = ! $active && now()->lt($trialEnd) ? (int) ceil(now()->diffInSeconds($trialEnd) / 86400) : 0;
        $status = match ($raw) {
            'active', 'halted', 'paused', 'cancelled', 'completed', 'expired' => $raw,
            'pending', 'created', 'authenticated' => 'pending',
            null, '' => $days > 0 ? 'trial' : 'trial_expired',
            default => 'unknown',
        };
        $plan = config('saas.plans.'.$tenant->plan);

        return [
            'status' => $status, 'label' => self::STATUSES[$status],
            'badge' => match ($status) {
                'active' => 'success', 'trial' => 'info', 'pending' => 'warning', default => 'secondary'
            },
            'trial_ends_at' => $trialEnd, 'trial_days_remaining' => $days,
            'trial_label' => $active ? 'Paid subscription recorded' : ($days > 0 ? $days.' day'.($days === 1 ? '' : 's').' left in trial' : 'Trial expired'),
            'raw_status' => $raw ?: 'Not started',
            'plan_name' => $plan['name'] ?? ucfirst($tenant->plan ?: 'Unassigned'),
            'monthly_price' => $plan['monthly_price'] ?? null,
            'attention' => in_array($status, ['trial_expired', 'pending', 'halted', 'paused', 'unknown'], true),
            'theme_name' => config('storefront_themes.'.app(StorefrontTheme::class)->normalize($tenant->theme).'.name'),
        ];
    }

    public function query(): Builder
    {
        return Tenant::query()->with(['admin' => fn ($query) => $query->withoutGlobalScopes()]);
    }

    public function filter(Builder $query, array $filters): Builder
    {
        if ($search = $filters['q'] ?? null) {
            $query->where(function (Builder $query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')->orWhere('slug', 'like', '%'.$search.'%')
                    ->orWhere('domain', 'like', '%'.$search.'%')
                    ->orWhereHas('admin', fn ($admin) => $admin->withoutGlobalScopes()->where(function ($owner) use ($search) {
                        $owner->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%');
                    }));
            });
        }
        if ($plan = $filters['plan'] ?? null) {
            $query->where('plan', $plan);
        }
        if ($status = $filters['status'] ?? null) {
            $this->statusFilter($query, $status);
        }
        if (($filters['trial_ending'] ?? '') === '1') {
            $this->unpaid($query);
            $query->where('created_at', '>', now()->subDays(config('saas.trial_days', 7)))
                ->where('created_at', '<=', now()->addDays(2)->subDays(config('saas.trial_days', 7)));
        }

        return $query;
    }

    public function statusFilter(Builder $query, string $status): void
    {
        if (in_array($status, ['trial', 'trial_expired'], true)) {
            $query->where(fn ($q) => $q->whereNull('subscription_status')->orWhere('subscription_status', ''))
                ->where('created_at', $status === 'trial' ? '>' : '<=', now()->subDays(config('saas.trial_days', 7)));
        } elseif ($status === 'pending') {
            $query->whereIn('subscription_status', ['pending', 'created', 'authenticated']);
        } elseif ($status === 'unknown') {
            $query->whereNotNull('subscription_status')->whereNotIn('subscription_status', ['', 'active', 'pending', 'created', 'authenticated', 'halted', 'paused', 'cancelled', 'completed', 'expired']);
        } else {
            $query->where('subscription_status', $status);
        }
    }

    private function unpaid(Builder $query): void
    {
        $query->where(fn ($q) => $q->whereNull('subscription_status')->orWhere('subscription_status', '!=', 'active'));
    }

    public function counts(): array
    {
        $counts = ['total' => Tenant::count()];
        foreach (array_keys(self::STATUSES) as $status) {
            $query = Tenant::query();
            $this->statusFilter($query, $status);
            $counts[$status] = $query->count();
        }
        $counts['trial_ending'] = $this->filter(Tenant::query(), ['trial_ending' => '1'])->count();
        $counts['unverified'] = Tenant::whereHas('admin', fn ($q) => $q->withoutGlobalScopes()->whereNull('email_verified_at'))->count();

        return $counts;
    }
}
