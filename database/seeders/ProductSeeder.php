<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Paket Basic',
                'description' => 'Paket layanan dasar untuk kebutuhan bisnis kecil',
                'price' => 500000,
                'duration' => '1 month',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Premium',
                'description' => 'Paket layanan premium dengan fitur lengkap',
                'price' => 1000000,
                'duration' => '1 month',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Enterprise',
                'description' => 'Paket layanan enterprise untuk perusahaan besar',
                'price' => 2500000,
                'duration' => '1 month',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Trial',
                'description' => 'Paket percobaan gratis selama 1 minggu',
                'price' => 0,
                'duration' => '1 week',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Paket Bulanan',
                'description' => 'Paket layanan bulanan dengan diskon khusus',
                'price' => 750000,
                'duration' => '1 month',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
