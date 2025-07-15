<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\PaymentConfirmations;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // 1. Laporan Transaksi Pesanan
    public function orderTransactions(Request $request)
    {
        $orders = Order::with(['user', 'orderItems', 'paymentConfirmations'])
            ->orderBy('order_date', 'desc')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    // 2. Laporan Pendapatan Hari Ini (berdasarkan payment confirmations yang approved)
    public function incomeToday()
    {
        $today = Carbon::today();
        $total = PaymentConfirmations::whereDate('updated_at', $today)
            ->where('status', 'approved')
            ->sum('amount');
        $count = PaymentConfirmations::whereDate('updated_at', $today)
            ->where('status', 'approved')
            ->count();
        return response()->json([
            'success' => true,
            'total_income_today' => $total,
            'transaction_count_today' => $count,
            'date' => $today->format('Y-m-d')
        ]);
    }

    // 3. Laporan Pendapatan Bulanan (berdasarkan payment confirmations yang approved)
    public function incomeMonthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $total = PaymentConfirmations::whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->where('status', 'approved')
            ->sum('amount');
        $count = PaymentConfirmations::whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->where('status', 'approved')
            ->count();
        return response()->json([
            'success' => true,
            'month' => $month,
            'year' => $year,
            'total_income_month' => $total,
            'transaction_count_month' => $count,
            'period' => Carbon::createFromDate($year, $month, 1)->format('F Y')
        ]);
    }

    // 4. Perbandingan Laporan Pendapatan Bulanan (1 tahun terakhir)
    public function incomeComparison()
    {
        $year = Carbon::now()->year;
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $total = PaymentConfirmations::whereMonth('updated_at', $m)
                ->whereYear('updated_at', $year)
                ->where('status', 'approved')
                ->sum('amount');
            $data[] = [
                'month' => $m,
                'month_name' => Carbon::createFromDate($year, $m, 1)->format('F'),
                'total_income' => $total
            ];
        }
        return response()->json([
            'success' => true,
            'year' => $year,
            'monthly_income' => $data
        ]);
    }

    // Export Laporan Transaksi Pesanan ke PDF
    public function exportOrderTransactionsPdf(Request $request)
    {
        $orders = Order::with(['user', 'orderItems', 'paymentConfirmations'])
            ->orderBy('order_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.orders_pdf', compact('orders'));
        return $pdf->download('laporan_transaksi_pesanan.pdf');
    }

    // Laporan Omzet/Penjualan Hari Ini
    public function omzetToday()
    {
        $today = Carbon::today();
        $total = Order::whereDate('order_date', $today)
            ->where('status', 'selesai')
            ->sum('total_amount');
        $count = Order::whereDate('order_date', $today)
            ->where('status', 'selesai')
            ->count();
        return response()->json([
            'success' => true,
            'total_omzet_today' => $total,
            'order_count_today' => $count
        ]);
    }

    // Laporan Omzet/Penjualan Bulanan
    public function omzetMonthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        $total = Order::whereMonth('order_date', $month)
            ->whereYear('order_date', $year)
            ->where('status', 'selesai')
            ->sum('total_amount');
        $count = Order::whereMonth('order_date', $month)
            ->whereYear('order_date', $year)
            ->where('status', 'selesai')
            ->count();
        return response()->json([
            'success' => true,
            'month' => $month,
            'year' => $year,
            'total_omzet_month' => $total,
            'order_count_month' => $count
        ]);
    }

    // Perbandingan Omzet Bulanan (1 tahun terakhir)
    public function omzetComparison()
    {
        $year = Carbon::now()->year;
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $total = Order::whereMonth('order_date', $m)
                ->whereYear('order_date', $year)
                ->where('status', 'selesai')
                ->sum('total_amount');
            $data[] = [
                'month' => $m,
                'total_omzet' => $total
            ];
        }
        return response()->json([
            'success' => true,
            'year' => $year,
            'monthly_omzet' => $data
        ]);
    }

    // Export Omzet/Penjualan ke PDF
    public function exportOmzetOrdersPdf(Request $request)
    {
        $orders = Order::where('status', 'selesai')
            ->orderBy('order_date', 'desc')
            ->get();
        $pdf = Pdf::loadView('reports.omzet_orders_pdf', compact('orders'));
        return $pdf->download('laporan_omzet_pesanan.pdf');
    }

    // 5. Laporan Pendapatan Detail (berdasarkan payment confirmations yang approved)
    public function incomeDetail(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());
        $limit = $request->get('limit', 10);

        $query = PaymentConfirmations::with(['order.user', 'paymentMethod'])
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startDate, $endDate]);

        $data = $query->orderBy('updated_at', 'desc')->paginate($limit);

        $totalIncome = $query->sum('amount');
        $totalTransactions = $query->count();

        return response()->json([
            'success' => true,
            'data' => $data->items(),
            'totalData' => $data->total(),
            'page' => $data->currentPage(),
            'limit' => $data->perPage(),
            'summary' => [
                'total_income' => $totalIncome,
                'total_transactions' => $totalTransactions,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    // 6. Laporan Pendapatan per Payment Method
    public function incomeByPaymentMethod(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $data = PaymentConfirmations::with('paymentMethod')
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->select('payment_method_id', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->groupBy('payment_method_id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    // 7. Dashboard Pendapatan
    public function incomeDashboard()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        $incomeToday = PaymentConfirmations::whereDate('updated_at', $today)
            ->where('status', 'approved')
            ->sum('amount');

        $incomeThisMonth = PaymentConfirmations::where('updated_at', '>=', $thisMonth)
            ->where('status', 'approved')
            ->sum('amount');

        $incomeLastMonth = PaymentConfirmations::whereBetween('updated_at', [$lastMonth, $thisMonth])
            ->where('status', 'approved')
            ->sum('amount');

        $pendingPayments = PaymentConfirmations::where('status', 'pending')->count();
        $approvedPayments = PaymentConfirmations::where('status', 'approved')->count();

        return response()->json([
            'success' => true,
            'data' => [
                'income_today' => $incomeToday,
                'income_this_month' => $incomeThisMonth,
                'income_last_month' => $incomeLastMonth,
                'pending_payments' => $pendingPayments,
                'approved_payments' => $approvedPayments,
                'growth_percentage' => $incomeLastMonth > 0 ? 
                    round((($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100, 2) : 0
            ]
        ]);
    }

    // 8. Export Laporan Pendapatan ke PDF
    public function exportIncomePdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $payments = PaymentConfirmations::with(['order.user', 'paymentMethod'])
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderBy('updated_at', 'desc')
            ->get();

        $totalIncome = $payments->sum('amount');
        $totalTransactions = $payments->count();

        $pdf = Pdf::loadView('reports.income_pdf', compact('payments', 'totalIncome', 'totalTransactions', 'startDate', 'endDate'));
        return $pdf->download('laporan_pendapatan.pdf');
    }

    // 9. Debug Payment Confirmations (untuk troubleshooting)
    public function debugPaymentConfirmations(Request $request)
    {
        try {
            $today = Carbon::today();
            
            // Cek semua payment confirmations
            $allConfirmations = PaymentConfirmations::with(['order.user', 'paymentMethod'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Cek payment confirmations yang approved hari ini
            $approvedToday = PaymentConfirmations::whereDate('updated_at', $today)
                ->where('status', 'approved')
                ->get();

            // Cek payment confirmations yang pending
            $pendingConfirmations = PaymentConfirmations::where('status', 'pending')->get();

            // Cek total pendapatan hari ini
            $incomeToday = PaymentConfirmations::whereDate('updated_at', $today)
                ->where('status', 'approved')
                ->sum('amount');

            // Cek total pendapatan bulan ini
            $incomeThisMonth = PaymentConfirmations::where('updated_at', '>=', Carbon::now()->startOfMonth())
                ->where('status', 'approved')
                ->sum('amount');

            return response()->json([
                'success' => true,
                'debug_info' => [
                    'today_date' => $today->format('Y-m-d H:i:s'),
                    'total_payment_confirmations' => $allConfirmations->count(),
                    'approved_today_count' => $approvedToday->count(),
                    'pending_count' => $pendingConfirmations->count(),
                    'income_today' => $incomeToday,
                    'income_this_month' => $incomeThisMonth,
                    'all_confirmations' => $allConfirmations->map(function($confirmation) {
                        return [
                            'confirmation_id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'amount' => $confirmation->amount,
                            'status' => $confirmation->status,
                            'created_at' => $confirmation->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $confirmation->updated_at->format('Y-m-d H:i:s'),
                            'customer_name' => $confirmation->order->user->name ?? 'N/A',
                            'payment_method' => $confirmation->paymentMethod->method_name ?? 'N/A'
                        ];
                    }),
                    'approved_today_details' => $approvedToday->map(function($confirmation) {
                        return [
                            'confirmation_id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'amount' => $confirmation->amount,
                            'updated_at' => $confirmation->updated_at->format('Y-m-d H:i:s'),
                            'customer_name' => $confirmation->order->user->name ?? 'N/A'
                        ];
                    }),
                    'pending_details' => $pendingConfirmations->map(function($confirmation) {
                        return [
                            'confirmation_id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'amount' => $confirmation->amount,
                            'created_at' => $confirmation->created_at->format('Y-m-d H:i:s'),
                            'customer_name' => $confirmation->order->user->name ?? 'N/A'
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 10. Simple Debug (tanpa authentication untuk testing)
    public function simpleDebug()
    {
        try {
            $today = Carbon::today();
            $now = Carbon::now();
            
            // Cek semua payment confirmations
            $allConfirmations = PaymentConfirmations::with(['order.user', 'paymentMethod'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Cek payment confirmations yang approved hari ini
            $approvedToday = PaymentConfirmations::whereDate('updated_at', $today)
                ->where('status', 'approved')
                ->get();

            // Cek payment confirmations yang pending
            $pendingConfirmations = PaymentConfirmations::where('status', 'pending')->get();

            // Cek total pendapatan hari ini
            $incomeToday = PaymentConfirmations::whereDate('updated_at', $today)
                ->where('status', 'approved')
                ->sum('amount');

            return response()->json([
                'success' => true,
                'message' => 'Debug Info - Pendapatan Hari Ini',
                'data' => [
                    'timezone_info' => [
                        'app_timezone' => config('app.timezone'),
                        'current_time' => $now->format('Y-m-d H:i:s'),
                        'today_date' => $today->format('Y-m-d H:i:s'),
                        'php_timezone' => date_default_timezone_get()
                    ],
                    'tanggal_hari_ini' => $today->format('Y-m-d H:i:s'),
                    'total_payment_confirmations' => $allConfirmations->count(),
                    'approved_hari_ini' => $approvedToday->count(),
                    'pending_count' => $pendingConfirmations->count(),
                    'pendapatan_hari_ini' => $incomeToday,
                    'detail_payment_confirmations' => $allConfirmations->map(function($confirmation) {
                        return [
                            'id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'jumlah' => $confirmation->amount,
                            'status' => $confirmation->status,
                            'tanggal_buat' => $confirmation->created_at->format('Y-m-d H:i:s'),
                            'tanggal_update' => $confirmation->updated_at->format('Y-m-d H:i:s'),
                            'customer' => $confirmation->order->user->name ?? 'N/A',
                            'metode_pembayaran' => $confirmation->paymentMethod->method_name ?? 'N/A'
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Debug gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 11. Fix Timezone Issue (untuk memperbaiki data yang salah tanggal)
    public function fixTimezoneIssue()
    {
        try {
            // Ambil semua payment confirmations yang approved
            $approvedConfirmations = PaymentConfirmations::where('status', 'approved')->get();
            
            $fixedCount = 0;
            $errors = [];
            
            foreach ($approvedConfirmations as $confirmation) {
                try {
                    // Jika tanggal update adalah tanggal yang salah (misal 2025-07-10)
                    // Update ke tanggal hari ini
                    if ($confirmation->updated_at->format('Y-m-d') === '2025-07-10') {
                        $confirmation->updated_at = Carbon::now();
                        $confirmation->save();
                        $fixedCount++;
                    }
                } catch (Exception $e) {
                    $errors[] = "Error fixing confirmation ID {$confirmation->confirmation_id}: " . $e->getMessage();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Timezone fix completed',
                'data' => [
                    'fixed_count' => $fixedCount,
                    'total_approved' => $approvedConfirmations->count(),
                    'errors' => $errors
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fix failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 12. Test Income Today (tanpa authentication)
    public function testIncomeToday()
    {
        try {
            $today = Carbon::today();
            
            // Cek total pendapatan hari ini
            $incomeToday = PaymentConfirmations::whereDate('updated_at', $today)
                ->where('status', 'approved')
                ->sum('amount');

            // Cek detail payment confirmations yang approved hari ini
            $approvedToday = PaymentConfirmations::whereDate('updated_at', $today)
                ->where('status', 'approved')
                ->with(['order.user', 'paymentMethod'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Test Income Today',
                'data' => [
                    'tanggal_hari_ini' => $today->format('Y-m-d H:i:s'),
                    'pendapatan_hari_ini' => $incomeToday,
                    'jumlah_transaksi' => $approvedToday->count(),
                    'detail_transaksi' => $approvedToday->map(function($confirmation) {
                        return [
                            'id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'jumlah' => $confirmation->amount,
                            'customer' => $confirmation->order->user->name ?? 'N/A',
                            'metode_pembayaran' => $confirmation->paymentMethod->method_name ?? 'N/A',
                            'tanggal_approved' => $confirmation->updated_at->format('Y-m-d H:i:s')
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 13. Test Income Yesterday (tanpa authentication)
    public function testIncomeYesterday()
    {
        try {
            $yesterday = Carbon::yesterday();
            
            // Cek total pendapatan kemarin
            $incomeYesterday = PaymentConfirmations::whereDate('updated_at', $yesterday)
                ->where('status', 'approved')
                ->sum('amount');

            // Cek detail payment confirmations yang approved kemarin
            $approvedYesterday = PaymentConfirmations::whereDate('updated_at', $yesterday)
                ->where('status', 'approved')
                ->with(['order.user', 'paymentMethod'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Test Income Yesterday',
                'data' => [
                    'tanggal_kemarin' => $yesterday->format('Y-m-d H:i:s'),
                    'pendapatan_kemarin' => $incomeYesterday,
                    'jumlah_transaksi' => $approvedYesterday->count(),
                    'detail_transaksi' => $approvedYesterday->map(function($confirmation) {
                        return [
                            'id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'jumlah' => $confirmation->amount,
                            'customer' => $confirmation->order->user->name ?? 'N/A',
                            'metode_pembayaran' => $confirmation->paymentMethod->method_name ?? 'N/A',
                            'tanggal_approved' => $confirmation->updated_at->format('Y-m-d H:i:s')
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 14. Income Yesterday (untuk frontend)
    public function incomeYesterday()
    {
        try {
            $yesterday = Carbon::yesterday();
            
            // Cek total pendapatan kemarin
            $incomeYesterday = PaymentConfirmations::whereDate('updated_at', $yesterday)
                ->where('status', 'approved')
                ->sum('amount');

            // Cek detail payment confirmations yang approved kemarin
            $approvedYesterday = PaymentConfirmations::whereDate('updated_at', $yesterday)
                ->where('status', 'approved')
                ->with(['order.user', 'paymentMethod'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Income Yesterday',
                'data' => [
                    'tanggal' => $yesterday->format('Y-m-d'),
                    'total_income' => $incomeYesterday,
                    'transaction_count' => $approvedYesterday->count(),
                    'transactions' => $approvedYesterday->map(function($confirmation) {
                        return [
                            'confirmation_id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'amount' => $confirmation->amount,
                            'customer_name' => $confirmation->order->user->name ?? 'N/A',
                            'payment_method' => $confirmation->paymentMethod->method_name ?? 'N/A',
                            'approved_at' => $confirmation->updated_at->format('Y-m-d H:i:s')
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get income yesterday',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // 15. Income This Week (untuk frontend)
    public function incomeThisWeek()
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            
            // Cek total pendapatan minggu ini
            $incomeThisWeek = PaymentConfirmations::whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->where('status', 'approved')
                ->sum('amount');

            // Cek detail payment confirmations yang approved minggu ini
            $approvedThisWeek = PaymentConfirmations::whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->where('status', 'approved')
                ->with(['order.user', 'paymentMethod'])
                ->orderBy('updated_at', 'desc')
                ->get();

            // Group by day
            $dailyIncome = PaymentConfirmations::whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->where('status', 'approved')
                ->selectRaw('DATE(updated_at) as date, SUM(amount) as total_amount, COUNT(*) as transaction_count')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Income This Week',
                'data' => [
                    'period' => [
                        'start' => $startOfWeek->format('Y-m-d'),
                        'end' => $endOfWeek->format('Y-m-d')
                    ],
                    'total_income' => $incomeThisWeek,
                    'transaction_count' => $approvedThisWeek->count(),
                    'daily_breakdown' => $dailyIncome->map(function($day) {
                        return [
                            'date' => $day->date,
                            'total_amount' => $day->total_amount,
                            'transaction_count' => $day->transaction_count
                        ];
                    }),
                    'transactions' => $approvedThisWeek->map(function($confirmation) {
                        return [
                            'confirmation_id' => $confirmation->confirmation_id,
                            'order_id' => $confirmation->order_id,
                            'amount' => $confirmation->amount,
                            'customer_name' => $confirmation->order->user->name ?? 'N/A',
                            'payment_method' => $confirmation->paymentMethod->method_name ?? 'N/A',
                            'approved_at' => $confirmation->updated_at->format('Y-m-d H:i:s')
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get income this week',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 