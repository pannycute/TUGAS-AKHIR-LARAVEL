# Panduan Debug Sederhana

## Cara Debug Masalah Pendapatan

### **Langkah 1: Cek Payment Confirmations**

Buka Postman atau browser, lalu akses:

```
GET http://localhost:8000/api/paymentconfirmations
Authorization: Bearer {token_admin}
```

**Yang harus Anda lihat:**
- List semua payment confirmations
- Status masing-masing (pending/approved/rejected)
- Tanggal created_at dan updated_at

### **Langkah 2: Cek Payment Confirmations Pending**

```
GET http://localhost:8000/api/paymentconfirmations/pending
Authorization: Bearer {token_admin}
```

**Yang harus Anda lihat:**
- List payment confirmations dengan status 'pending'
- Jika kosong, berarti tidak ada yang perlu dikonfirmasi

### **Langkah 3: Cek Pendapatan Hari Ini**

```
GET http://localhost:8000/api/reports/income/today
Authorization: Bearer {token_admin}
```

**Yang harus Anda lihat:**
- `total_income_today`: Jumlah pendapatan hari ini
- `transaction_count_today`: Jumlah transaksi hari ini

### **Langkah 4: Cek Debug Info (Paling Penting)**

```
GET http://localhost:8000/api/reports/income/debug
Authorization: Bearer {token_admin}
```

**Yang harus Anda lihat:**
- `total_payment_confirmations`: Total semua payment confirmations
- `approved_today_count`: Jumlah yang approved hari ini
- `pending_count`: Jumlah yang masih pending
- `income_today`: Pendapatan hari ini
- `all_confirmations`: Detail semua payment confirmations

## Contoh Response yang Benar

### Jika Ada Payment Confirmation yang Approved:
```json
{
    "success": true,
    "debug_info": {
        "today_date": "2025-01-16 00:00:00",
        "total_payment_confirmations": 2,
        "approved_today_count": 1,
        "pending_count": 1,
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
            },
            {
                "confirmation_id": 2,
                "order_id": 2,
                "amount": 150000,
                "status": "pending",
                "created_at": "2025-01-16 12:00:00",
                "updated_at": "2025-01-16 12:00:00",
                "customer_name": "Jane Smith",
                "payment_method": "E-Wallet"
            }
        ]
    }
}
```

### Jika Tidak Ada Data:
```json
{
    "success": true,
    "debug_info": {
        "today_date": "2025-01-16 00:00:00",
        "total_payment_confirmations": 0,
        "approved_today_count": 0,
        "pending_count": 0,
        "income_today": 0,
        "income_this_month": 0,
        "all_confirmations": []
    }
}
```

## Cara Menggunakan Postman

### 1. Buka Postman
### 2. Buat Request Baru
### 3. Pilih Method: GET
### 4. Masukkan URL: `http://localhost:8000/api/reports/income/debug`
### 5. Di tab Headers, tambahkan:
   - Key: `Authorization`
   - Value: `Bearer {token_admin}`
### 6. Klik Send
### 7. Lihat response di bagian bawah

## Cara Menggunakan Browser (Chrome/Firefox)

### 1. Buka browser
### 2. Install extension "REST Client" atau "Postman"
### 3. Atau gunakan Developer Tools:
   - Tekan F12
   - Pilih tab Console
   - Ketik:
   ```javascript
   fetch('http://localhost:8000/api/reports/income/debug', {
       headers: {
           'Authorization': 'Bearer {token_admin}'
       }
   })
   .then(response => response.json())
   .then(data => console.log(data));
   ```

## Troubleshooting

### Jika Response Error 401:
- Token admin salah atau expired
- Ganti dengan token admin yang baru

### Jika Response Error 404:
- URL salah
- Pastikan server Laravel berjalan di `http://localhost:8000`

### Jika Response Error 500:
- Ada error di server
- Cek log Laravel di `storage/logs/laravel.log`

### Jika `total_payment_confirmations = 0`:
- Belum ada payment confirmation yang dibuat
- User perlu upload bukti pembayaran dulu

### Jika `approved_today_count = 0`:
- Belum ada yang dikonfirmasi hari ini
- Admin perlu konfirmasi payment confirmation

### Jika `income_today = 0` padahal ada yang approved:
- Ada masalah dengan query pendapatan
- Kemungkinan masalah timezone

## Langkah Selanjutnya

Setelah Anda mendapatkan response dari debug endpoint, copy paste response tersebut ke saya. Saya akan membantu menganalisis masalahnya dan memberikan solusi yang tepat. 