<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class CheckOrderDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:check-due-dates {--days=3 : Number of days to check ahead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders that are due soon and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $this->info("Checking orders due within {$days} days...");

        // Get orders due within specified days
        $orders = Order::with('user')
            ->dueWithinDays($days)
            ->where('status', '!=', 'selesai')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders due soon.');
            return;
        }

        $this->info("Found {$orders->count()} orders due soon:");

        foreach ($orders as $order) {
            $daysRemaining = $order->days_remaining;
            $status = $daysRemaining > 0 ? "Due in {$daysRemaining} days" : "Overdue by " . abs($daysRemaining) . " days";
            
            $this->line("- Order #{$order->order_id} (User: {$order->user->name}) - {$status}");
            
            // Here you can add notification logic
            // For example: send email, push notification, etc.
            $this->sendNotification($order, $daysRemaining);
        }

        $this->info('Due date check completed.');
    }

    /**
     * Send notification for order due soon
     */
    private function sendNotification($order, $daysRemaining)
    {
        // Example notification logic
        // You can implement email, SMS, or push notification here
        
        if ($daysRemaining <= 0) {
            // Order is overdue
            $this->warn("⚠️  Order #{$order->order_id} is OVERDUE!");
        } elseif ($daysRemaining <= 1) {
            // Order due tomorrow
            $this->warn("⚠️  Order #{$order->order_id} due TOMORROW!");
        } else {
            // Order due soon
            $this->info("📅 Order #{$order->order_id} due in {$daysRemaining} days");
        }
    }
} 