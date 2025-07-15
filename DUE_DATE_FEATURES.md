# Fitur Tenggat Waktu (Due Date) - Sistem Order

## Overview
Sistem tenggat waktu memungkinkan tracking order berdasarkan durasi paket yang dibeli. Setiap produk memiliki durasi tertentu, dan order akan otomatis memiliki due date berdasarkan durasi produk.

## Fitur yang Tersedia

### 1. **Automatic Due Date Calculation**
- Due date dihitung otomatis saat order dibuat
- Menggunakan duration terpanjang jika ada multiple produk
- Formula: `due_date = order_date + max_duration`

### 2. **Order Status Tracking**
- **`is_overdue`**: Boolean yang menunjukkan apakah order sudah melewati due date
- **`days_remaining`**: Jumlah hari tersisa (negatif jika sudah overdue)

### 3. **Filter dan Endpoint**

#### Filter pada GET /api/orders
```
GET /api/orders?filter=overdue
GET /api/orders?filter=due_soon&days=7
GET /api/orders?status=pending
```

#### Endpoint Khusus
```
GET /api/orders/overdue          # Order yang sudah overdue
GET /api/orders/due-soon?days=7  # Order yang akan due dalam 7 hari
GET /api/orders/dashboard        # Statistik dashboard
```

### 4. **Command untuk Notifikasi**
```bash
php artisan orders:check-due-dates --days=3
```

## Database Schema

### Tabel `products`
```sql
ALTER TABLE products ADD COLUMN duration INT DEFAULT 30 COMMENT 'Duration in days';
```

### Tabel `orders`
```sql
ALTER TABLE orders ADD COLUMN due_date DATETIME NULL AFTER order_date;
```

## Contoh Penggunaan

### 1. Membuat Produk dengan Duration
```json
POST /api/products
{
    "name": "Paket Premium 1 Bulan",
    "description": "Paket premium dengan durasi 1 bulan",
    "price": 500000,
    "stock": 100,
    "duration": 30
}
```

### 2. Membuat Order (Due Date Otomatis)
```json
POST /api/orders
{
    "user_id": 1,
    "order_date": "2025-01-15 10:00:00",
    "status": "pending",
    "items": [
        {
            "product_id": 1,
            "quantity": 1
        }
    ]
}
```

### 3. Response Order dengan Due Date
```json
{
    "success": true,
    "data": {
        "order_id": 1,
        "user_id": 1,
        "order_date": "2025-01-15T10:00:00.000000Z",
        "due_date": "2025-02-14T10:00:00.000000Z",
        "status": "pending",
        "total_amount": 500000,
        "is_overdue": false,
        "days_remaining": 15
    }
}
```

### 4. Filter Order Overdue
```json
GET /api/orders/overdue

Response:
{
    "success": true,
    "data": [
        {
            "order_id": 2,
            "due_date": "2025-01-10T10:00:00.000000Z",
            "is_overdue": true,
            "days_remaining": -5
        }
    ],
    "message": "Orders that are overdue"
}
```

### 5. Dashboard Statistics
```json
GET /api/orders/dashboard

Response:
{
    "success": true,
    "data": {
        "total_orders": 10,
        "pending_orders": 5,
        "overdue_orders": 2,
        "due_soon_orders": 3
    }
}
```

## Model Methods

### Order Model
```php
// Accessors
$order->is_overdue;      // Boolean
$order->days_remaining;  // Integer (negative if overdue)

// Scopes
Order::overdue();                    // Filter overdue orders
Order::dueWithinDays(7);            // Filter orders due within 7 days
Order::byStatus('pending');         // Filter by status
```

## Command Usage

### Manual Check
```bash
# Check orders due within 3 days (default)
php artisan orders:check-due-dates

# Check orders due within 7 days
php artisan orders:check-due-dates --days=7
```

### Automated Check (Cron Job)
Tambahkan ke crontab untuk check otomatis setiap hari:
```bash
0 9 * * * cd /path/to/project && php artisan orders:check-due-dates
```

## Testing

### Seeder
```bash
php artisan db:seed --class=OrderSeeder
```

Seeder akan membuat:
- 3 produk dengan duration berbeda (7, 30, 90 hari)
- 4 order dengan due date berbeda (overdue, due soon, due later, due tomorrow)

## Migration

Jalankan migration untuk menambahkan kolom baru:
```bash
php artisan migrate
```

## Notifikasi (Future Enhancement)

Sistem dapat diperluas dengan:
- Email notification
- SMS notification
- Push notification
- Webhook integration
- Slack/Discord integration

Implementasi dapat ditambahkan di method `sendNotification()` pada command `CheckOrderDueDates`. 