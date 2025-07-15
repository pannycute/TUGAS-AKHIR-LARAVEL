# Debug Laporan Pendapatan

## Masalah: Pendapatan tidak bertambah setelah admin konfirmasi pembayaran

Mari kita debug step by step untuk menemukan masalahnya.

## Step 1: Cek Debug Info

Pertama, akses endpoint debug untuk melihat status payment confirmations:

```bash
GET /api/reports/income/debug
Authorization: Bearer {admin_token}
```

**Response yang diharapkan:**
```json
{
    "success": true,
    "debug_info": {
        "today_date": "2025-01-16 00:00:00",
        "total_payment_confirmations": 3,
        "approved_today_count": 1,
        "pending_count": 2,
        "income_today": 100000,
        "income_this_month": 100000,
        "all_confirmations": [
            {
                "confirmation_id": 1,
                "order_id": 1,
                "amount": 100000,
                "status": "approved",
                "created_at": "2025-01-16 10:30:00",
                "updated_at": "2025-01-16 11:00:00",
                "customer_name": "John Doe",
                "payment_method": "Transfer Bank"
            }
        ],
        "approved_today_details": [
            {
                "confirmation_id": 1,
                "order_id": 1,
                "amount": 100000,
                "updated_at": "2025-01-16 11:00:00",
                "customer_name": "John Doe"
            }
        ],
        "pending_details": [
            {
                "confirmation_id": 2,
                "order_id": 2,
                "amount": 150000,
                "created_at": "2025-01-16 12:00:00",
                "customer_name": "Jane Smith"
            }
        ]
    }
}
```

## Step 2: Analisis Debug Info

### Jika `approved_today_count = 0`:
- Artinya belum ada payment confirmation yang approved hari ini
- Cek apakah admin sudah benar-benar mengkonfirmasi pembayaran

### Jika `approved_today_count > 0` tapi `income_today = 0`:
- Ada masalah dengan query pendapatan
- Kemungkinan masalah dengan timezone atau format tanggal

### Jika `pending_count > 0`:
- Ada pembayaran yang masih pending
- Admin perlu mengkonfirmasi pembayaran tersebut

## Step 3: Test Konfirmasi Pembayaran

### 3.1. Cek Payment Confirmations Pending
```bash
GET /api/paymentconfirmations/pending
Authorization: Bearer {admin_token}
```

### 3.2. Konfirmasi Pembayaran
```bash
PATCH /api/paymentconfirmations/{id}/confirm
Authorization: Bearer {admin_token}
```

**Response yang diharapkan:**
```json
{
    "success": true,
    "message": "Konfirmasi pembayaran berhasil dan pendapatan telah dicatat",
    "data": {
        "confirmation_id": 1,
        "status": "approved",
        "updated_at": "2025-01-16 14:30:00"
    }
}
```

### 3.3. Cek Pendapatan Setelah Konfirmasi
```bash
GET /api/reports/income/today
Authorization: Bearer {admin_token}
```

## Step 4: Kemungkinan Masalah dan Solusi

### Masalah 1: Timezone
**Gejala:** Payment confirmed tapi tidak muncul di laporan hari ini
**Solusi:** Cek apakah `updated_at` sesuai dengan tanggal hari ini

### Masalah 2: Status Tidak Berubah
**Gejala:** Status tetap 'pending' setelah konfirmasi
**Solusi:** Cek apakah ada error saat update status

### Masalah 3: Database Migration
**Gejala:** Error saat mengakses payment confirmations
**Solusi:** Jalankan migration untuk memastikan tabel sudah benar

## Step 5: Test Manual

### 5.1. Buat Payment Confirmation Manual
```bash
POST /api/payment/user-confirm
Authorization: Bearer {user_token}
Content-Type: application/json

{
    "order_id": 1,
    "payment_method_id": 1,
    "amount": 50000,
    "confirmation_date": "2025-01-16",
    "proof_image": "test.jpg"
}
```

### 5.2. Konfirmasi Sebagai Admin
```bash
PATCH /api/paymentconfirmations/{confirmation_id}/confirm
Authorization: Bearer {admin_token}
```

### 5.3. Cek Hasil
```bash
GET /api/reports/income/debug
Authorization: Bearer {admin_token}
```

## Step 6: Cek Database Langsung

Jika masih bermasalah, cek database langsung:

```sql
-- Cek semua payment confirmations
SELECT * FROM payment_confirmations ORDER BY created_at DESC;

-- Cek payment confirmations yang approved hari ini
SELECT * FROM payment_confirmations 
WHERE DATE(updated_at) = CURDATE() 
AND status = 'approved';

-- Cek total pendapatan hari ini
SELECT SUM(amount) as total_income 
FROM payment_confirmations 
WHERE DATE(updated_at) = CURDATE() 
AND status = 'approved';
```

## Step 7: Logging

Tambahkan logging untuk debug lebih detail:

```php
// Di PaymentConfirmationsController::confirm()
\Log::info('Payment confirmation approved', [
    'confirmation_id' => $paymentConfirmation->confirmation_id,
    'amount' => $paymentConfirmation->amount,
    'updated_at' => $paymentConfirmation->updated_at,
    'status' => $paymentConfirmation->status
]);
```

## Step 8: Checklist Debug

- [ ] Payment confirmation berhasil dibuat dengan status 'pending'
- [ ] Admin berhasil mengkonfirmasi (status berubah ke 'approved')
- [ ] `updated_at` timestamp terupdate saat konfirmasi
- [ ] Query laporan pendapatan menggunakan `updated_at` yang benar
- [ ] Timezone server sesuai dengan lokasi
- [ ] Tidak ada error di log Laravel

## Step 9: Test Sederhana

### Test 1: Cek Status Payment Confirmation
```bash
GET /api/paymentconfirmations/{id}
Authorization: Bearer {admin_token}
```

### Test 2: Cek Dashboard Pendapatan
```bash
GET /api/reports/income/dashboard
Authorization: Bearer {admin_token}
```

### Test 3: Cek Detail Pendapatan
```bash
GET /api/reports/income/detail?start_date=2025-01-16&end_date=2025-01-16
Authorization: Bearer {admin_token}
```

## Troubleshooting

### Jika semua test gagal:
1. Cek apakah ada error di log Laravel (`storage/logs/laravel.log`)
2. Cek apakah database connection berfungsi
3. Cek apakah migration sudah dijalankan dengan benar
4. Cek apakah ada masalah dengan model relationships

### Jika beberapa test berhasil:
1. Cek apakah ada masalah dengan query specific
2. Cek apakah ada masalah dengan timezone
3. Cek apakah ada masalah dengan format tanggal

## Contoh Response Debug yang Benar

```json
{
    "success": true,
    "debug_info": {
        "today_date": "2025-01-16 00:00:00",
        "total_payment_confirmations": 1,
        "approved_today_count": 1,
        "pending_count": 0,
        "income_today": 100000,
        "income_this_month": 100000,
        "all_confirmations": [
            {
                "confirmation_id": 1,
                "order_id": 1,
                "amount": 100000,
                "status": "approved",
                "created_at": "2025-01-16 10:30:00",
                "updated_at": "2025-01-16 11:00:00",
                "customer_name": "John Doe",
                "payment_method": "Transfer Bank"
            }
        ]
    }
}
```

Jika response debug menunjukkan data yang benar tapi laporan pendapatan masih 0, kemungkinan ada masalah dengan query di method laporan pendapatan. 