<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan</title>
    <style>
        body { 
            font-family: sans-serif; 
            font-size: 12px; 
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px;
        }
        th, td { 
            border: 1px solid #333; 
            padding: 8px; 
            text-align: left;
        }
        th { 
            background: #eee; 
            font-weight: bold;
        }
        .total-row {
            background: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Pendapatan</h2>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>

    <div class="summary">
        <h3>Ringkasan</h3>
        <p><strong>Total Pendapatan:</strong> Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        <p><strong>Total Transaksi:</strong> {{ $totalTransactions }} transaksi</p>
        <p><strong>Rata-rata per Transaksi:</strong> Rp {{ $totalTransactions > 0 ? number_format($totalIncome / $totalTransactions, 0, ',', '.') : 0 }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Konfirmasi</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Metode Pembayaran</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $i => $payment)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->updated_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $payment->order->order_id ?? '-' }}</td>
                <td>{{ $payment->order->user->name ?? '-' }}</td>
                <td>{{ $payment->paymentMethod->method_name ?? '-' }}</td>
                <td>Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align: right;"><strong>Total:</strong></td>
                <td><strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat otomatis pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistem Order Management</p>
    </div>
</body>
</html> 