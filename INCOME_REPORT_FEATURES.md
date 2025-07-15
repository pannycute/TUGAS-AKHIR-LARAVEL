# Fitur Laporan Pendapatan

## Overview
Fitur laporan pendapatan memungkinkan admin untuk melihat dan menganalisis pendapatan yang masuk ketika admin mengkonfirmasi pembayaran user. Pendapatan dihitung berdasarkan payment confirmations yang memiliki status 'approved'.

## Flow Proses Pendapatan

### 1. User Upload Bukti Pembayaran
- User upload bukti pembayaran → Status: `pending`
- Pendapatan belum dicatat

### 2. Admin Konfirmasi Pembayaran
- Admin konfirmasi pembayaran → Status: `approved`
- **Pendapatan otomatis dicatat** pada saat konfirmasi
- Timestamp konfirmasi disimpan di `updated_at`

### 3. Laporan Pendapatan
- Admin dapat melihat laporan pendapatan berdasarkan payment confirmations yang approved
- Pendapatan dihitung berdasarkan `updated_at` (waktu konfirmasi)

## Endpoints Laporan Pendapatan

### 1. Laporan Pendapatan Hari Ini
```
GET /api/reports/income/today
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "total_income_today": 500000,
    "transaction_count_today": 3,
    "date": "2025-01-16"
}
```

### 2. Laporan Pendapatan Bulanan
```
GET /api/reports/income/monthly?month=1&year=2025
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "month": 1,
    "year": 2025,
    "total_income_month": 2500000,
    "transaction_count_month": 15,
    "period": "January 2025"
}
```

### 3. Perbandingan Pendapatan Bulanan
```
GET /api/reports/income/comparison
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "year": 2025,
    "monthly_income": [
        {
            "month": 1,
            "month_name": "January",
            "total_income": 2500000
        },
        {
            "month": 2,
            "month_name": "February",
            "total_income": 3000000
        }
    ]
}
```

### 4. Laporan Pendapatan Detail
```
GET /api/reports/income/detail?start_date=2025-01-01&end_date=2025-01-31&limit=10
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "confirmation_id": 1,
            "order_id": 1,
            "payment_method_id": 1,
            "amount": "100000.00",
            "status": "approved",
            "updated_at": "2025-01-16T10:30:00.000000Z",
            "order": {
                "order_id": 1,
                "user": {
                    "user_id": 1,
                    "name": "John Doe"
                }
            },
            "payment_method": {
                "payment_method_id": 1,
                "method_name": "Transfer Bank"
            }
        }
    ],
    "totalData": 15,
    "page": 1,
    "limit": 10,
    "summary": {
        "total_income": 2500000,
        "total_transactions": 15,
        "start_date": "2025-01-01",
        "end_date": "2025-01-31"
    }
}
```

### 5. Laporan Pendapatan per Metode Pembayaran
```
GET /api/reports/income/by-payment-method?start_date=2025-01-01&end_date=2025-01-31
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "payment_method_id": 1,
            "total_amount": 1500000,
            "transaction_count": 10,
            "payment_method": {
                "payment_method_id": 1,
                "method_name": "Transfer Bank"
            }
        },
        {
            "payment_method_id": 2,
            "total_amount": 1000000,
            "transaction_count": 5,
            "payment_method": {
                "payment_method_id": 2,
                "method_name": "E-Wallet"
            }
        }
    ],
    "period": {
        "start_date": "2025-01-01",
        "end_date": "2025-01-31"
    }
}
```

### 6. Dashboard Pendapatan
```
GET /api/reports/income/dashboard
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "income_today": 500000,
        "income_this_month": 2500000,
        "income_last_month": 2000000,
        "pending_payments": 5,
        "approved_payments": 25,
        "growth_percentage": 25.0
    }
}
```

### 7. Export Laporan Pendapatan ke PDF
```
GET /api/reports/income/export/pdf?start_date=2025-01-01&end_date=2025-01-31
Authorization: Bearer {admin_token}
```

**Response:** File PDF yang dapat didownload

## Perbedaan dengan Laporan Omzet

### Laporan Pendapatan (Income)
- Berdasarkan **payment confirmations yang approved**
- Menghitung uang yang **benar-benar masuk** ke kas
- Menggunakan timestamp **konfirmasi admin** (`updated_at`)
- Lebih akurat untuk laporan keuangan

### Laporan Omzet (Sales)
- Berdasarkan **order yang selesai**
- Menghitung nilai **penjualan** (bukan uang yang masuk)
- Menggunakan timestamp **order** (`order_date`)
- Lebih cocok untuk analisis penjualan

## Cara Penggunaan

### Untuk Admin:

1. **Lihat Dashboard Pendapatan:**
   ```
   GET /api/reports/income/dashboard
   ```

2. **Lihat Pendapatan Hari Ini:**
   ```
   GET /api/reports/income/today
   ```

3. **Lihat Pendapatan Bulanan:**
   ```
   GET /api/reports/income/monthly?month=1&year=2025
   ```

4. **Lihat Detail Transaksi:**
   ```
   GET /api/reports/income/detail?start_date=2025-01-01&end_date=2025-01-31
   ```

5. **Export ke PDF:**
   ```
   GET /api/reports/income/export/pdf?start_date=2025-01-01&end_date=2025-01-31
   ```

## Testing

### 1. Test Konfirmasi Pembayaran dan Pendapatan
```bash
# 1. User upload bukti pembayaran
POST /api/payment/user-confirm
{
    "order_id": 1,
    "payment_method_id": 1,
    "amount": 100000,
    "confirmation_date": "2025-01-16",
    "proof_image": "bukti.jpg"
}

# 2. Admin konfirmasi pembayaran
PATCH /api/paymentconfirmations/1/confirm

# 3. Cek pendapatan hari ini
GET /api/reports/income/today
```

### 2. Test Laporan Detail
```bash
# Lihat detail pendapatan bulan ini
GET /api/reports/income/detail?start_date=2025-01-01&end_date=2025-01-31
```

### 3. Test Dashboard
```bash
# Lihat dashboard pendapatan
GET /api/reports/income/dashboard
```

## Database Schema

### Tabel `payment_confirmations`
```sql
- confirmation_id (Primary Key)
- order_id (Foreign Key)
- payment_method_id (Foreign Key)
- amount (Decimal)
- confirmation_date (DateTime)
- status (Enum: pending, approved, rejected)
- proof_image (String)
- created_at (Timestamp)
- updated_at (Timestamp) -- Waktu konfirmasi admin
```

## Controller Methods

### ReportController
- `incomeToday()` - Pendapatan hari ini
- `incomeMonthly()` - Pendapatan bulanan
- `incomeComparison()` - Perbandingan bulanan
- `incomeDetail()` - Detail transaksi
- `incomeByPaymentMethod()` - Per metode pembayaran
- `incomeDashboard()` - Dashboard pendapatan
- `exportIncomePdf()` - Export ke PDF

## View Templates

### `resources/views/reports/income_pdf.blade.php`
- Template untuk laporan PDF
- Menampilkan ringkasan dan detail transaksi
- Format yang rapi dan profesional

## Keunggulan Sistem

1. **Akurasi Tinggi**: Pendapatan hanya dicatat setelah admin konfirmasi
2. **Timestamp Presisi**: Menggunakan waktu konfirmasi admin
3. **Laporan Komprehensif**: Berbagai jenis laporan tersedia
4. **Export PDF**: Laporan dapat diexport untuk keperluan administrasi
5. **Dashboard Real-time**: Statistik pendapatan real-time
6. **Filter Fleksibel**: Filter berdasarkan tanggal dan periode 