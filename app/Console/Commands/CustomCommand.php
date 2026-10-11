<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CustomCommand extends Command
{
    protected $signature = 'ecommerce:check-low-stock {--threshold=5}';

    protected $description = 'List products with low stock';

    public function handle(): int
    {
        $threshold = filter_var($this->option('threshold'), FILTER_VALIDATE_INT);

        if ($threshold === false || $threshold < 0) {
            $this->error('The threshold must be a non-negative integer.');

            return self::FAILURE;
        }

        $products = Product::query()
            ->where('stock', '<=', $threshold)
            ->orderBy('stock')
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'stock']);

        $productRows = $products->map(fn (Product $product): array => [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'stock' => $product->stock,
        ])->all();

        Log::info('Low-stock product check completed.', [
            'threshold' => $threshold,
            'low_stock_count' => $products->count(),
            'products' => $productRows,
        ]);

        if ($products->isEmpty()) {
            $this->info("No products have stock at or below {$threshold}.");
            return self::SUCCESS;
        }

        $this->table(['ID', 'Product', 'SKU', 'Stock'], $products->map(
            fn (Product $product): array => [$product->id, $product->name, $product->sku, $product->stock]
        ));

        $this->warn($products->count().' product(s) have low stock.');

        return self::SUCCESS;
    }
}
