# Contoh Response API

## 1. User Upload Bukti Pembayaran
**Request:**
```
POST /api/payment/user-confirm
Authorization: Bearer {token}
Content-Type: application/json

{
    "order_id": 1,
    "payment_method_id": 1,
    "amount": 100000,
    "confirmation_date": "2025-01-16",
    "proof_image": "bukti_pembayaran.jpg"
}
```

**Response (201):**
```json
{
    "success": true,
    "data": {
        "confirmation_id": 1,
        "order_id": 1,
        "payment_method_id": 1,
        "amount": "100000.00",
        "confirmation_date": "2025-01-16T00:00:00.000000Z",
        "status": "pending",
        "proof_image": "bukti_pembayaran.jpg",
        "created_at": "2025-01-16T10:30:00.000000Z",
        "updated_at": "2025-01-16T10:30:00.000000Z"
    },
    "message": "Konfirmasi pembayaran berhasil dikirim, menunggu verifikasi admin."
}
```

## 2. User Lihat Order Milik Sendiri
**Request:**
```
GET /api/orders/my-orders
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "order_id": 1,
            "user_id": 1,
            "order_date": "2025-01-15T10:00:00.000000Z",
            "due_date": "2025-01-22T10:00:00.000000Z",
            "status": "confirmed",
            "total_amount": "100000.00",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-16T10:30:00.000000Z",
            "order_items": [
                {
                    "order_item_id": 1,
                    "order_id": 1,
                    "product_id": 1,
                    "quantity": 2,
                    "unit_price": "50000.00",
                    "subtotal": "100000.00",
                    "product": {
                        "product_id": 1,
                        "name": "Produk A",
                        "description": "Deskripsi produk A",
                        "price": "50000.00"
                    }
                }
            ],
            "payment_confirmations": [
                {
                    "confirmation_id": 1,
                    "order_id": 1,
                    "payment_method_id": 1,
                    "amount": "100000.00",
                    "confirmation_date": "2025-01-16T00:00:00.000000Z",
                    "status": "approved",
                    "proof_image": "bukti_pembayaran.jpg",
                    "payment_method": {
                        "payment_method_id": 1,
                        "method_name": "Transfer Bank",
                        "details": "Bank BCA - 1234567890"
                    }
                }
            ]
        }
    ],
    "totalData": 1,
    "page": 1,
    "limit": 10
}
```

## 3. Admin Lihat Payment Confirmations Pending
**Request:**
```
GET /api/paymentconfirmations/pending
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "confirmation_id": 1,
            "order_id": 1,
            "payment_method_id": 1,
            "amount": "100000.00",
            "confirmation_date": "2025-01-16T00:00:00.000000Z",
            "status": "pending",
            "proof_image": "bukti_pembayaran.jpg",
            "created_at": "2025-01-16T10:30:00.000000Z",
            "updated_at": "2025-01-16T10:30:00.000000Z",
            "order": {
                "order_id": 1,
                "user_id": 1,
                "order_date": "2025-01-15T10:00:00.000000Z",
                "due_date": "2025-01-22T10:00:00.000000Z",
                "status": "pending",
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
    "totalData": 1,
    "page": 1,
    "limit": 10
}
```

## 4. Admin Konfirmasi Pembayaran
**Request:**
```
PATCH /api/paymentconfirmations/1/confirm
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Konfirmasi pembayaran berhasil",
    "data": {
        "confirmation_id": 1,
        "order_id": 1,
        "payment_method_id": 1,
        "amount": "100000.00",
        "confirmation_date": "2025-01-16T00:00:00.000000Z",
        "status": "approved",
        "proof_image": "bukti_pembayaran.jpg",
        "created_at": "2025-01-16T10:30:00.000000Z",
        "updated_at": "2025-01-16T11:00:00.000000Z",
        "order": {
            "order_id": 1,
            "user_id": 1,
            "order_date": "2025-01-15T10:00:00.000000Z",
            "due_date": "2025-01-22T10:00:00.000000Z",
            "status": "confirmed",
            "total_amount": "100000.00"
        },
        "payment_method": {
            "payment_method_id": 1,
            "method_name": "Transfer Bank",
            "details": "Bank BCA - 1234567890"
        }
    }
}
```

## 5. Admin Tolak Pembayaran
**Request:**
```
PATCH /api/paymentconfirmations/1/reject
Authorization: Bearer {admin_token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Pembayaran ditolak",
    "data": {
        "confirmation_id": 1,
        "order_id": 1,
        "payment_method_id": 1,
        "amount": "100000.00",
        "confirmation_date": "2025-01-16T00:00:00.000000Z",
        "status": "rejected",
        "proof_image": "bukti_pembayaran.jpg",
        "created_at": "2025-01-16T10:30:00.000000Z",
        "updated_at": "2025-01-16T11:00:00.000000Z",
        "order": {
            "order_id": 1,
            "user_id": 1,
            "order_date": "2025-01-15T10:00:00.000000Z",
            "due_date": "2025-01-22T10:00:00.000000Z",
            "status": "pending",
            "total_amount": "100000.00"
        },
        "payment_method": {
            "payment_method_id": 1,
            "method_name": "Transfer Bank",
            "details": "Bank BCA - 1234567890"
        }
    }
}
```

## 6. User Cek Status Order Setelah Konfirmasi
**Request:**
```
GET /api/orders/my-orders?status=confirmed
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "order_id": 1,
            "user_id": 1,
            "order_date": "2025-01-15T10:00:00.000000Z",
            "due_date": "2025-01-22T10:00:00.000000Z",
            "status": "confirmed",
            "total_amount": "100000.00",
            "created_at": "2025-01-15T10:00:00.000000Z",
            "updated_at": "2025-01-16T11:00:00.000000Z",
            "order_items": [
                {
                    "order_item_id": 1,
                    "order_id": 1,
                    "product_id": 1,
                    "quantity": 2,
                    "unit_price": "50000.00",
                    "subtotal": "100000.00",
                    "product": {
                        "product_id": 1,
                        "name": "Produk A",
                        "description": "Deskripsi produk A",
                        "price": "50000.00"
                    }
                }
            ],
            "payment_confirmations": [
                {
                    "confirmation_id": 1,
                    "order_id": 1,
                    "payment_method_id": 1,
                    "amount": "100000.00",
                    "confirmation_date": "2025-01-16T00:00:00.000000Z",
                    "status": "approved",
                    "proof_image": "bukti_pembayaran.jpg",
                    "payment_method": {
                        "payment_method_id": 1,
                        "method_name": "Transfer Bank",
                        "details": "Bank BCA - 1234567890"
                    }
                }
            ]
        }
    ],
    "totalData": 1,
    "page": 1,
    "limit": 10
}
```

## Error Responses

### 404 - Payment Confirmation Not Found
```json
{
    "success": false,
    "message": "Payment confirmation not found"
}
```

### 403 - Unauthorized (Non-Admin)
```json
{
    "success": false,
    "message": "Unauthorized. Admins only."
}
```

### 422 - Validation Error
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "order_id": ["The order id field is required."],
        "amount": ["The amount must be a number."]
    }
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