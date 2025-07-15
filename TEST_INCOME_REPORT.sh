#!/bin/bash

# Script Testing untuk Debug Laporan Pendapatan
# Ganti {admin_token} dan {user_token} dengan token yang sesuai

echo "=== TEST DEBUG LAPORAN PENDAPATAN ==="
echo ""

# Step 1: Cek Debug Info
echo "1. Cek Debug Info Payment Confirmations..."
curl -X GET "http://localhost:8000/api/reports/income/debug" \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo "2. Cek Payment Confirmations Pending..."
curl -X GET "http://localhost:8000/api/paymentconfirmations/pending" \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo "3. Cek Pendapatan Hari Ini..."
curl -X GET "http://localhost:8000/api/reports/income/today" \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo "4. Cek Dashboard Pendapatan..."
curl -X GET "http://localhost:8000/api/reports/income/dashboard" \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo "=== INSTRUKSI TESTING ==="
echo ""
echo "Jika ada payment confirmations pending, konfirmasi dengan:"
echo "curl -X PATCH \"http://localhost:8000/api/paymentconfirmations/{id}/confirm\" \\"
echo "  -H \"Authorization: Bearer {admin_token}\""
echo ""
echo "Kemudian cek lagi debug info untuk melihat perubahan."
echo ""
echo "Jika tidak ada data, buat payment confirmation baru:"
echo "curl -X POST \"http://localhost:8000/api/payment/user-confirm\" \\"
echo "  -H \"Authorization: Bearer {user_token}\" \\"
echo "  -H \"Content-Type: application/json\" \\"
echo "  -d '{"
echo "    \"order_id\": 1,"
echo "    \"payment_method_id\": 1,"
echo "    \"amount\": 100000,"
echo "    \"confirmation_date\": \"$(date +%Y-%m-%d)\","
echo "    \"proof_image\": \"test.jpg\""
echo "  }'" 