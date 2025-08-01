<?php
namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\PaymentConfirmations;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function createPayment(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'amount' => 'required|numeric',
                'customer_name' => 'required|string',
                'customer_email' => 'required|email',
            ]);

            $order = Order::findOrFail($request->order_id);
            
            // Siapkan parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . $order->order_id,
                    'gross_amount' => $order->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $request->customer_name,
                    'email' => $request->customer_email,
                ],
                'item_details' => [
                    [
                        'id' => $order->order_id,
                        'price' => $order->total_amount,
                        'quantity' => 1,
                        'name' => 'Order #' . $order->order_id,
                    ]
                ],
                'enabled_payments' => [
                    'qris'
                ],
            ];

            // Dapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'payment_url' => "https://app.midtrans.com/snap/v2/vtweb/{$snapToken}",
                'message' => 'Payment URL generated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment: ' . $e->getMessage()
            ], 500);
        }
    }

    
 
    
    public function handleWebhook(Request $request)
    {
        try {
            $json = $request->getContent();
            $signatureKey = $request->header('X-Signature-Key');
            
            // Verifikasi signature (opsional tapi recommended)
            if (!$this->verifySignature($json, $signatureKey)) {
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $notification = json_decode($json, true);
            
            $orderId = $notification['order_id'];
            $status = $notification['transaction_status'];
            $fraudStatus = $notification['fraud_status'];
            
            // Extract order ID dari format "ORDER-123"
            $realOrderId = str_replace('ORDER-', '', $orderId);
            
            $order = Order::where('order_id', $realOrderId)->first();
            
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Update status berdasarkan notifikasi Midtrans
            if ($status == 'capture' || $status == 'settlement') {
                if ($fraudStatus == 'challenge') {
                    // TODO: Handle challenge
                    $order->update(['status' => 'pending']);
                } else if ($fraudStatus == 'accept') {
                    // Payment berhasil
                    $order->update(['status' => 'proses']);
                    // Bagian otomatis membuat payment confirmation dihapus
                }
            } else if ($status == 'cancel' || $status == 'deny' || $status == 'expire') {
                $order->update(['status' => 'cancelled']);
            } else if ($status == 'pending') {
                $order->update(['status' => 'pending']);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Webhook processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function verifySignature($json, $signatureKey)
    {
        $expectedSignature = hash('sha512', $json . config('midtrans.server_key'));
        return $expectedSignature === $signatureKey;
    }

    public function checkPaymentStatus($orderId)
    {
        try {
            $order = Order::where('order_id', $orderId)->first();
            
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Cek status payment confirmation
            $paymentConfirmation = PaymentConfirmations::where('order_id', $orderId)
                ->where('status', 'approved')
                ->first();

            return response()->json([
                'success' => true,
                'order_status' => $order->status,
                'payment_status' => $paymentConfirmation ? 'approved' : 'pending',
                'payment_confirmation' => $paymentConfirmation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to check payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function userConfirmPayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'payment_method_id' => 'required|exists:payment_methods,payment_method_id',
            'amount' => 'required|numeric|min:0',
            'confirmation_date' => 'required|date',
            'proof_image' => 'nullable|string|max:255',
        ]);

        $confirmation = PaymentConfirmations::create([
            'order_id' => $request->order_id,
            'payment_method_id' => $request->payment_method_id,
            'amount' => $request->amount,
            'confirmation_date' => $request->confirmation_date,
            'status' => 'pending',
            'proof_image' => $request->proof_image,
        ]);

        return response()->json([
            'success' => true,
            'data' => $confirmation,
            'message' => 'Konfirmasi pembayaran berhasil dikirim, menunggu verifikasi admin.'
        ], 201);
    }
}