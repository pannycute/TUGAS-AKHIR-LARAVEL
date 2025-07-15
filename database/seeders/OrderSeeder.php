<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample products with different durations
        $products = [
            [
                'name' => 'Paket Basic 1 Bulan',
                'description' => 'Paket basic dengan durasi 1 bulan',
                'price' => 300000,
                'stock' => 50,
                'duration' => 30
            ],
            [
                'name' => 'Paket Premium 3 Bulan',
                'description' => 'Paket premium dengan durasi 3 bulan',
                'price' => 800000,
                'stock' => 30,
                'duration' => 90
            ],
            [
                'name' => 'Paket Trial 7 Hari',
                'description' => 'Paket trial dengan durasi 7 hari',
                'price' => 50000,
                'stock' => 100,
                'duration' => 7
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Create sample orders with different due dates
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $orders = [
            [
                'user_id' => $users->first()->user_id,
                'order_date' => Carbon::now()->subDays(25),
                'due_date' => Carbon::now()->addDays(5), // Due soon
                'status' => 'pending',
                'total_amount' => 300000
            ],
            [
                'user_id' => $users->first()->user_id,
                'order_date' => Carbon::now()->subDays(35),
                'due_date' => Carbon::now()->subDays(5), // Overdue
                'status' => 'proses',
                'total_amount' => 800000
            ],
            [
                'user_id' => $users->first()->user_id,
                'order_date' => Carbon::now()->subDays(5),
                'due_date' => Carbon::now()->addDays(25), // Due later
                'status' => 'pending',
                'total_amount' => 50000
            ],
            [
                'user_id' => $users->first()->user_id,
                'order_date' => Carbon::now()->subDays(10),
                'due_date' => Carbon::now()->addDays(1), // Due tomorrow
                'status' => 'pending',
                'total_amount' => 300000
            ]
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);
            
            // Create order items
            $product = Product::inRandomOrder()->first();
            OrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'quantity' => 1,
                'unit_price' => $product->price,
                'subtotal' => $product->price
            ]);
        }

        $this->command->info('Order seeder completed successfully!');
    }
} 