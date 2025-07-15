# Fitur Konfirmasi Pembayaran

## Overview
Fitur ini memungkinkan user untuk mengupload bukti pembayaran dan admin untuk mengkonfirmasi atau menolak pembayaran tersebut. Ketika admin mengkonfirmasi pembayaran, status order akan berubah menjadi 'confirmed'.

## Flow Proses

### 1. User Upload Bukti Pembayaran
- User membuat order
- User melakukan pembayaran
- User upload bukti pembayaran melalui endpoint `/api/payment/user-confirm`
- Status payment confirmation: `pending`
- Status order: `pending`

### 2. Admin Review Pembayaran
- Admin dapat melihat semua payment confirmations yang pending
- Admin dapat mengkonfirmasi atau menolak pembayaran

### 3. Admin Konfirmasi Pembayaran
- Admin mengakses endpoint `/api/paymentconfirmations/{id}/confirm`
- Status payment confirmation berubah menjadi: `approved`
- Status order berubah menjadi: `confirmed`

### 4. Admin Tolak Pembayaran
- Admin mengakses endpoint `/api/paymentconfirmations/{id}/reject`
- Status payment confirmation berubah menjadi: `rejected`
- Status order kembali menjadi: `pending`

## Endpoints

### User Endpoints
```
POST /api/payment/user-confirm
```
**Request Body:**
```json
{
    "order_id": 1,
    "payment_method_id": 1,
    "amount": 100000,
    "confirmation_date": "2025-01-16",
    "proof_image": "path/to/image.jpg"
}
```

### Admin Endpoints
```
PATCH /api/paymentconfirmations/{id}/confirm
PATCH /api/paymentconfirmations/{id}/reject
```

## Status Order
- `pending`: Order baru dibuat atau pembayaran ditolak
- `proses`: Order sedang diproses
- `selesai`: Order selesai
- `confirmed`: Pembayaran sudah dikonfirmasi admin

## Status Payment Confirmation
- `pending`: Bukti pembayaran sudah diupload, menunggu konfirmasi admin
- `approved`: Pembayaran dikonfirmasi admin
- `rejected`: Pembayaran ditolak admin

## Database Changes
1. Migration: `2025_01_16_000001_add_confirmed_status_to_orders_table.php`
   - Menambahkan status 'confirmed' ke enum status di tabel orders

## Controller Changes
1. **PaymentConfirmationsController**
   - Method `confirm()`: Mengubah status payment confirmation menjadi 'approved' dan order menjadi 'confirmed'
   - Method `reject()`: Mengubah status payment confirmation menjadi 'rejected' dan order kembali menjadi 'pending'

2. **PaymentController**
   - Method `userConfirmPayment()`: Untuk user upload bukti pembayaran

## Model Changes
1. **Order Model**
   - Memperbaiki relasi `paymentConfirmations()` untuk menggunakan model yang benar

## Routes
- `/api/payment/user-confirm` (POST) - User upload bukti pembayaran
- `/api/paymentconfirmations/pending` (GET) - Admin lihat payment confirmations yang pending
- `/api/paymentconfirmations/{id}/confirm` (PATCH) - Admin konfirmasi pembayaran
- `/api/paymentconfirmations/{id}/reject` (PATCH) - Admin tolak pembayaran
- `/api/orders/my-orders` (GET) - User lihat order milik sendiri

## Cara Penggunaan

### Untuk User:
1. Setelah membuat order, user melakukan pembayaran
2. User upload bukti pembayaran melalui endpoint `/api/payment/user-confirm`
3. User menunggu konfirmasi dari admin
4. Setelah admin konfirmasi, status order akan berubah menjadi 'confirmed'

### Untuk Admin:
1. Admin melihat daftar payment confirmations yang pending
2. Admin review bukti pembayaran
3. Admin dapat mengkonfirmasi atau menolak pembayaran
4. Jika dikonfirmasi, status order berubah menjadi 'confirmed'
5. Jika ditolak, status order kembali menjadi 'pending'

## Testing
Untuk testing, Anda dapat menggunakan Postman atau tools API testing lainnya:

1. **Test User Upload Bukti Pembayaran:**
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

2. **Test User Lihat Order Milik Sendiri:**
   ```
   GET /api/orders/my-orders
   Authorization: Bearer {token}
   ```

3. **Test Admin Lihat Payment Confirmations Pending:**
   ```
   GET /api/paymentconfirmations/pending
   Authorization: Bearer {admin_token}
   ```

4. **Test Admin Konfirmasi Pembayaran:**
   ```
   PATCH /api/paymentconfirmations/1/confirm
   Authorization: Bearer {admin_token}
   ```

5. **Test Admin Tolak Pembayaran:**
   ```
   PATCH /api/paymentconfirmations/1/reject
   Authorization: Bearer {admin_token}
   ```

6. **Test Cek Status Order Setelah Konfirmasi:**
   ```
   GET /api/orders/my-orders?status=confirmed
   Authorization: Bearer {token}
   ``` 