# Script Debug Sederhana untuk Windows
# Cara pakai: Klik kanan file ini, pilih "Run with PowerShell"

Write-Host "=== DEBUG PENDAPATAN SISTEM ORDER ===" -ForegroundColor Green
Write-Host ""

# Cek apakah server Laravel berjalan
Write-Host "1. Mengecek server Laravel..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000" -TimeoutSec 5
    Write-Host "   ✓ Server Laravel berjalan" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Server Laravel tidak berjalan!" -ForegroundColor Red
    Write-Host "   Jalankan: php artisan serve" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Tekan Enter untuk keluar"
    exit
}

Write-Host ""

# Cek debug endpoint
Write-Host "2. Mengecek data payment confirmations..." -ForegroundColor Yellow
try {
    $debugResponse = Invoke-WebRequest -Uri "http://localhost:8000/api/debug/simple" -TimeoutSec 10
    $debugData = $debugResponse.Content | ConvertFrom-Json
    
    if ($debugData.success) {
        Write-Host "   ✓ Debug endpoint berhasil" -ForegroundColor Green
        Write-Host ""
        
        $data = $debugData.data
        Write-Host "=== HASIL DEBUG ===" -ForegroundColor Cyan
        Write-Host "Tanggal Hari Ini: $($data.tanggal_hari_ini)" -ForegroundColor White
        Write-Host "Total Payment Confirmations: $($data.total_payment_confirmations)" -ForegroundColor White
        Write-Host "Approved Hari Ini: $($data.approved_hari_ini)" -ForegroundColor White
        Write-Host "Pending Count: $($data.pending_count)" -ForegroundColor White
        Write-Host "Pendapatan Hari Ini: Rp $($data.pendapatan_hari_ini.ToString('N0'))" -ForegroundColor White
        Write-Host ""
        
        # Analisis hasil
        Write-Host "=== ANALISIS ===" -ForegroundColor Cyan
        
        if ($data.total_payment_confirmations -eq 0) {
            Write-Host "❌ Masalah: Tidak ada payment confirmation" -ForegroundColor Red
            Write-Host "   Solusi: User perlu upload bukti pembayaran dulu" -ForegroundColor Yellow
        } elseif ($data.approved_hari_ini -eq 0) {
            Write-Host "❌ Masalah: Tidak ada yang approved hari ini" -ForegroundColor Red
            Write-Host "   Solusi: Admin perlu konfirmasi payment confirmation" -ForegroundColor Yellow
        } elseif ($data.pendapatan_hari_ini -eq 0) {
            Write-Host "❌ Masalah: Pendapatan 0 padahal ada yang approved" -ForegroundColor Red
            Write-Host "   Solusi: Kemungkinan masalah tanggal/timezone" -ForegroundColor Yellow
        } else {
            Write-Host "✅ Sistem berjalan normal!" -ForegroundColor Green
            Write-Host "   Pendapatan hari ini: Rp $($data.pendapatan_hari_ini.ToString('N0'))" -ForegroundColor Green
        }
        
        Write-Host ""
        Write-Host "=== DETAIL PAYMENT CONFIRMATIONS ===" -ForegroundColor Cyan
        if ($data.detail_payment_confirmations.Count -gt 0) {
            foreach ($confirmation in $data.detail_payment_confirmations) {
                Write-Host "ID: $($confirmation.id)" -ForegroundColor White
                Write-Host "  Order ID: $($confirmation.order_id)" -ForegroundColor Gray
                Write-Host "  Jumlah: Rp $($confirmation.jumlah.ToString('N0'))" -ForegroundColor Gray
                Write-Host "  Status: $($confirmation.status)" -ForegroundColor Gray
                Write-Host "  Customer: $($confirmation.customer)" -ForegroundColor Gray
                Write-Host "  Metode: $($confirmation.metode_pembayaran)" -ForegroundColor Gray
                Write-Host "  Tanggal Buat: $($confirmation.tanggal_buat)" -ForegroundColor Gray
                Write-Host "  Tanggal Update: $($confirmation.tanggal_update)" -ForegroundColor Gray
                Write-Host ""
            }
        } else {
            Write-Host "Tidak ada payment confirmations" -ForegroundColor Gray
        }
        
    } else {
        Write-Host "   ✗ Debug endpoint gagal" -ForegroundColor Red
        Write-Host "   Error: $($debugData.message)" -ForegroundColor Red
    }
    
} catch {
    Write-Host "   ✗ Gagal mengakses debug endpoint!" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== INSTRUKSI SELANJUTNYA ===" -ForegroundColor Cyan
Write-Host "1. Jika ada masalah, copy paste hasil di atas ke saya" -ForegroundColor White
Write-Host "2. Jika tidak ada payment confirmation, user perlu upload bukti pembayaran" -ForegroundColor White
Write-Host "3. Jika ada pending, admin perlu konfirmasi payment" -ForegroundColor White
Write-Host "4. Jika ada approved tapi pendapatan 0, ada masalah tanggal" -ForegroundColor White

Write-Host ""
Read-Host "Tekan Enter untuk keluar" 