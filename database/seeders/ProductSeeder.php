<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Wireless Mouse',
                'sku' => 'PROD-001',
                'price' => 15000,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mechanical Keyboard',
                'sku' => 'PROD-002',
                'price' => 30000,
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'USB-C Cable',
                'sku' => 'PROD-003',
                'price' => 17000,
                'stock' => 19,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop Stand',
                'sku' => 'PROD-004',
                'price' => 29000,
                'stock' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'USB Hub',
                'sku' => 'PROD-005',
                'price' => 19000,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Webcam HD',
                'sku' => 'PROD-006',
                'price' => 30000,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bluetooth Headphones',
                'sku' => 'PROD-007',
                'price' => 25000,
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'External SSD 1TB',
                'sku' => 'PROD-008',
                'price' => 99000,
                'stock' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monitor 24 Inch',
                'sku' => 'PROD-009',
                'price' => 14900,
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Laptop Backpack',
                'sku' => 'PROD-010',
                'price' => 34000,
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
