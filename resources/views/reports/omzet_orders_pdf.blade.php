<!DOCTYPE html>
<html>
<head>
    <title>Laporan Omzet/Penjualan Pesanan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #333; padding: 4px; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Laporan Omzet/Penjualan Pesanan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Pesanan</th>
                <th>Nama Customer</th>
                <th>Tanggal Order</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->user->name ?? '-' }}</td>
                <td>{{ $order->order_date }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 