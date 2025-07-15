# Contoh Response Laporan Pendapatan

## 1. Dashboard Pendapatan
**Request:**
```
GET /api/reports/income/dashboard
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "income_today": 750000,
        "income_this_month": 3500000,
        "income_last_month": 2800000,
        "pending_payments": 8,
        "approved_payments": 42,
        "growth_percentage": 25.0
    }
}
```

## 2. Pendapatan Hari Ini
**Request:**
```
GET /api/reports/income/today
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "total_income_today": 750000,
    "transaction_count_today": 4,
    "date": "2025-01-16"
}
```

## 3. Pendapatan Bulanan
**Request:**
```
GET /api/reports/income/monthly?month=1&year=2025
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "month": 1,
    "year": 2025,
    "total_income_month": 3500000,
    "transaction_count_month": 18,
    "period": "January 2025"
}
```

## 4. Perbandingan Pendapatan Bulanan
**Request:**
```
GET /api/reports/income/comparison
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "year": 2025,
    "monthly_income": [
        {
            "month": 1,
            "month_name": "January",
            "total_income": 3500000
        },
        {
            "month": 2,
            "month_name": "February",
            "total_income": 0
        },
        {
            "month": 3,
            "month_name": "March",
            "total_income": 0
        },
        {
            "month": 4,
            "month_name": "April",
            "total_income": 0
        },
        {
            "month": 5,
            "month_name": "May",
            "total_income": 0
        },
        {
            "month": 6,
            "month_name": "June",
            "total_income": 0
        },
        {
            "month": 7,
            "month_name": "July",
            "total_income": 0
        },
        {
            "month": 8,
            "month_name": "August",
            "total_income": 0
        },
        {
            "month": 9,
            "month_name": "September",
            "total_income": 0
        },
        {
            "month": 10,
            "month_name": "October",
            "total_income": 0
        },
        {
            "month": 11,
            "month_name": "November",
            "total_income": 0
        },
        {
            "month": 12,
            "month_name": "December",
            "total_income": 0
        }
    ]
}
```

## 5. Detail Pendapatan
**Request:**
```
GET /api/reports/income/detail?start_date=2025-01-01&end_date=2025-01-31&limit=5
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "confirmation_id": 5,
            "order_id": 5,
            "payment_method_id": 1,
            "amount": "200000.00",
            "confirmation_date": "2025-01-16T00:00:00.000000Z",
            "status": "approved",
            "proof_image": "bukti_pembayaran_5.jpg",
            "created_at": "2025-01-16T09:00:00.000000Z",
            "updated_at": "2025-01-16T10:30:00.000000Z",
            "order": {
                "order_id": 5,
                "user_id": 3,
                "order_date": "2025-01-15T14:00:00.000000Z",
                "due_date": "2025-01-22T14:00:00.000000Z",
                "status": "confirmed",
                "total_amount": "200000.00",
                "user": {
                    "user_id": 3,
                    "name": "Jane Smith",
                    "email": "jane@example.com"
                }
            },
            "payment_method": {
                "payment_method_id": 1,
                "method_name": "Transfer Bank",
                "details": "Bank BCA - 1234567890"
            }
        },
        {
            "confirmation_id": 4,
            "order_id": 4,
            "payment_method_id": 2,
            "amount": "150000.00",
            "confirmation_date": "2025-01-16T00:00:00.000000Z",
            "status": "approved",
            "proof_image": "bukti_pembayaran_4.jpg",
            "created_at": "2025-01-16T08:30:00.000000Z",
            "updated_at": "2025-01-16T10:15:00.000000Z",
            "order": {
                "order_id": 4,
                "user_id": 2,
                "order_date": "2025-01-15T13:30:00.000000Z",
                "due_date": "2025-01-22T13:30:00.000000Z",
                "status": "confirmed",
                "total_amount": "150000.00",
                "user": {
                    "user_id": 2,
                    "name": "Bob Johnson",
                    "email": "bob@example.com"
                }
            },
            "payment_method": {
                "payment_method_id": 2,
                "method_name": "E-Wallet",
                "details": "GoPay - 081234567890"
            }
        },
        {
            "confirmation_id": 3,
            "order_id": 3,
            "payment_method_id": 1,
            "amount": "100000.00",
            "confirmation_date": "2025-01-16T00:00:00.000000Z",
            "status": "approved",
            "proof_image": "bukti_pembayaran_3.jpg",
            "created_at": "2025-01-16T08:00:00.000000Z",
            "updated_at": "2025-01-16T10:00:00.000000Z",
            "order": {
                "order_id": 3,
                "user_id": 1,
                "order_date": "2025-01-15T13:00:00.000000Z",
                "due_date": "2025-01-22T13:00:00.000000Z",
                "status": "confirmed",
                "total_amount": "100000.00",
                "user": {
                    "user_id": 1,
                    "name": "John Doe",
                    "email": "john@example.com"
                }
            },
            "payment_method": {
                "payment_method_id": 1,
                "method_name": "Transfer Bank",
                "details": "Bank BCA - 1234567890"
            }
        }
    ],
    "totalData": 18,
    "page": 1,
    "limit": 5,
    "summary": {
        "total_income": 3500000,
        "total_transactions": 18,
        "start_date": "2025-01-01",
        "end_date": "2025-01-31"
    }
}
```

## 6. Pendapatan per Metode Pembayaran
**Request:**
```
GET /api/reports/income/by-payment-method?start_date=2025-01-01&end_date=2025-01-31
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "payment_method_id": 1,
            "total_amount": 2500000,
            "transaction_count": 12,
            "payment_method": {
                "payment_method_id": 1,
                "method_name": "Transfer Bank",
                "details": "Bank BCA - 1234567890"
            }
        },
        {
            "payment_method_id": 2,
            "total_amount": 1000000,
            "transaction_count": 6,
            "payment_method": {
                "payment_method_id": 2,
                "method_name": "E-Wallet",
                "details": "GoPay - 081234567890"
            }
        }
    ],
    "period": {
        "start_date": "2025-01-01",
        "end_date": "2025-01-31"
    }
}
```

## 7. Export PDF
**Request:**
```
GET /api/reports/income/export/pdf?start_date=2025-01-01&end_date=2025-01-31
Authorization: Bearer {admin_token}
```

**Response:** File PDF yang dapat didownload dengan nama `laporan_pendapatan.pdf`

## Testing Scenarios

### Scenario 1: Konfirmasi Pembayaran dan Cek Pendapatan
```bash
# 1. User upload bukti pembayaran
curl -X POST http://localhost:8000/api/payment/user-confirm \
  -H "Authorization: Bearer {user_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 1,
    "payment_method_id": 1,
    "amount": 100000,
    "confirmation_date": "2025-01-16",
    "proof_image": "bukti.jpg"
  }'

# 2. Admin konfirmasi pembayaran
curl -X PATCH http://localhost:8000/api/paymentconfirmations/1/confirm \
  -H "Authorization: Bearer {admin_token}"

# 3. Cek pendapatan hari ini
curl -X GET http://localhost:8000/api/reports/income/today \
  -H "Authorization: Bearer {admin_token}"
```

### Scenario 2: Multiple Konfirmasi dan Laporan
```bash
# 1. Konfirmasi beberapa pembayaran
curl -X PATCH http://localhost:8000/api/paymentconfirmations/1/confirm \
  -H "Authorization: Bearer {admin_token}"

curl -X PATCH http://localhost:8000/api/paymentconfirmations/2/confirm \
  -H "Authorization: Bearer {admin_token}"

curl -X PATCH http://localhost:8000/api/paymentconfirmations/3/confirm \
  -H "Authorization: Bearer {admin_token}"

# 2. Cek dashboard pendapatan
curl -X GET http://localhost:8000/api/reports/income/dashboard \
  -H "Authorization: Bearer {admin_token}"

# 3. Cek detail pendapatan
curl -X GET "http://localhost:8000/api/reports/income/detail?start_date=2025-01-01&end_date=2025-01-31" \
  -H "Authorization: Bearer {admin_token}"
```

### Scenario 3: Laporan Bulanan
```bash
# 1. Cek pendapatan bulan ini
curl -X GET "http://localhost:8000/api/reports/income/monthly?month=1&year=2025" \
  -H "Authorization: Bearer {admin_token}"

# 2. Cek perbandingan bulanan
curl -X GET http://localhost:8000/api/reports/income/comparison \
  -H "Authorization: Bearer {admin_token}"

# 3. Export ke PDF
curl -X GET "http://localhost:8000/api/reports/income/export/pdf?start_date=2025-01-01&end_date=2025-01-31" \
  -H "Authorization: Bearer {admin_token}" \
  --output laporan_pendapatan.pdf
```

## Error Responses

### 403 - Unauthorized (Non-Admin)
```json
{
    "success": false,
    "message": "Unauthorized. Admins only."
}
```

### 404 - No Data Found
```json
{
    "success": true,
    "total_income_today": 0,
    "transaction_count_today": 0,
    "date": "2025-01-16"
}
```

### 500 - Server Error
```json
{
    "success": false,
    "message": "Server error",
    "error": "Database connection failed"
}
```

## Notes

1. **Timestamp**: Semua laporan pendapatan menggunakan `updated_at` sebagai waktu konfirmasi admin
2. **Status**: Hanya payment confirmations dengan status 'approved' yang dihitung sebagai pendapatan
3. **Filter**: Semua endpoint mendukung filter tanggal untuk periode tertentu
4. **Pagination**: Endpoint detail mendukung pagination dengan parameter `limit` dan `page`
5. **Export**: PDF export tersedia untuk laporan detail dengan format yang rapi 