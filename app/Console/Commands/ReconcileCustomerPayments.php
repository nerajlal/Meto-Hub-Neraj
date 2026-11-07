<?php

namespace App\Console\Commands;

use App\Models\CustomerPayment;
use App\Services\CustomerPayments;
use Illuminate\Console\Command;

class ReconcileCustomerPayments extends Command
{
    protected $signature = 'payments:reconcile {--checkout= : Recheck a specific checkout UUID}';
    protected $description = 'Recover captured customer gateway payments without relying on browser callbacks';

    public function handle(CustomerPayments $payments): int
    {
        $query = CustomerPayment::whereIn('status', ['creating', 'creation_unknown', 'ready', 'captured', 'review', 'failed']);
        if ($id = $this->option('checkout')) $query->whereKey($id);
        else $query->where(fn ($q) => $q->whereNull('last_checked_at')->orWhere('last_checked_at', '<=', now()->subMinutes(5)));
        foreach ($query->orderBy('last_checked_at')->orderBy('created_at')->limit(100)->get() as $payment) {
            try {
                $result = $payments->reconcile($payment);
                $this->line($payment->id.': '.$result->status);
            } catch (\Throwable $e) {
                $payment->update(['last_checked_at' => now()]);
                $this->warn($payment->id.': provider check failed; will retry.');
            }
        }
        return self::SUCCESS;
    }
}
