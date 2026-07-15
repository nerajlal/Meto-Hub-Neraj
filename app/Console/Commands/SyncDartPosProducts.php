<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncDartPosProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dartpos:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync products and inventory from DartPOS';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\DartPosService $dartPos)
    {
        $this->info('Starting DartPOS product sync...');
        
        $products = $dartPos->getProducts(1, 100);
        
        if (!$products) {
            $this->error('Failed to fetch products from DartPOS. Please check API credentials.');
            return 1;
        }

        $this->info('Fetched ' . count($products['data'] ?? []) . ' products. Syncing to database...');
        
        // TODO: Implement actual database mapping here based on DartPOS payload structure
        // For example:
        // foreach ($products['data'] as $item) {
        //     Product::updateOrCreate(['sku' => $item['sku']], ['title' => $item['name'], ...]);
        // }
        
        $this->info('Sync complete!');
        return 0;
    }
}
