<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentConfirmations;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PaymentConfirmationsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $status = $request->get('status');
            
            $query = PaymentConfirmations::with(['order', 'paymentMethod']);
            
            // Filter berdasarkan status jika ada
            if ($status) {
                $query->where('status', $status);
            }
            
            $data = $query->orderBy('created_at', 'desc')->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'totalData' => $data->total(),
                'page' => $data->currentPage(),
                'limit' => $data->perPage(),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
    
    public function pendingConfirmations(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $data = PaymentConfirmations::with(['order.user', 'paymentMethod'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'totalData' => $data->total(),
                'page' => $data->currentPage(),
                'limit' => $data->perPage(),
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request)
    {
        // Log user info for debug
        \Log::info('PaymentConfirmation store accessed', [
            'user_id' => $request->user() ? $request->user()->user_id : null,
            'role' => $request->user() ? $request->user()->role : null,
        ]);
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'payment_method_id' => 'required|exists:payment_methods,payment_method_id',
                'amount' => 'required|numeric|min:0',
                'confirmation_date' => 'required|date',
                'status' => 'required|in:pending',
                'proof_image' => 'nullable|string|max:255',
            ]);

            $confirmation = PaymentConfirmations::create($validated);

            return response()->json([
                'success' => true,
                'data' => $confirmation,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show($id)
    {
        try {
            $confirmation = PaymentConfirmations::with(['order', 'paymentMethod'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $confirmation,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $confirmation = PaymentConfirmations::findOrFail($id);

            $validated = $request->validate([
                'order_id' => 'sometimes|exists:orders,order_id',
                'payment_method_id' => 'sometimes|exists:payment_methods,payment_method_id',
                'amount' => 'sometimes|numeric|min:0',
                'confirmation_date' => 'sometimes|date',
                'status' => 'sometimes|in:pending,proses,selesai',
                'proof_image' => 'nullable|string|max:255',
            ]);

            $confirmation->update($validated);

            return response()->json([
                'success' => true,
                'data' => $confirmation,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy($id)
    {
        try {
            $confirmation = PaymentConfirmations::findOrFail($id);
            $confirmation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmation deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
    public function confirm($id)
    {
        try {
            $paymentConfirmation = PaymentConfirmations::with(['order'])->findOrFail($id);
            
            // Log sebelum update
            \Log::info('Payment confirmation before update', [
                'confirmation_id' => $paymentConfirmation->confirmation_id,
                'order_id' => $paymentConfirmation->order_id,
                'amount' => $paymentConfirmation->amount,
                'status_before' => $paymentConfirmation->status,
                'updated_at_before' => $paymentConfirmation->updated_at
            ]);
            
            // Update status payment confirmation menjadi approved dengan timestamp
            $paymentConfirmation->status = 'approved';
            $paymentConfirmation->save();
            
            // Refresh data untuk mendapatkan updated_at yang baru
            $paymentConfirmation->refresh();
            
            // Update status order menjadi confirmed
            if ($paymentConfirmation->order) {
                $paymentConfirmation->order->update(['status' => 'confirmed']);
            }
            
            // Log setelah update
            \Log::info('Payment confirmation after update', [
                'confirmation_id' => $paymentConfirmation->confirmation_id,
                'status_after' => $paymentConfirmation->status,
                'updated_at_after' => $paymentConfirmation->updated_at,
                'order_status' => $paymentConfirmation->order->status ?? 'N/A'
            ]);
        
            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi pembayaran berhasil dan pendapatan telah dicatat',
                'data' => $paymentConfirmation->load(['order', 'paymentMethod'])
            ]);
        } catch (ModelNotFoundException $e) {
            \Log::error('Payment confirmation not found', ['id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation not found',
            ], 404);
        } catch (Exception $e) {
            \Log::error('Payment confirmation error', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse($e);
        }
    }
    
    public function reject($id)
    {
        try {
            $paymentConfirmation = PaymentConfirmations::with(['order'])->findOrFail($id);
            
            // Update status payment confirmation menjadi rejected
            $paymentConfirmation->status = 'rejected';
            $paymentConfirmation->save();
            
            // Update status order kembali menjadi pending
            if ($paymentConfirmation->order) {
                $paymentConfirmation->order->update(['status' => 'pending']);
            }
        
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran ditolak',
                'data' => $paymentConfirmation->load(['order', 'paymentMethod'])
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment confirmation not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }
    
    private function errorResponse($e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Server error',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
