<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentConfirmation;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PaymentConfirmationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $data = PaymentConfirmation::with(['order', 'paymentMethod'])->paginate($limit);

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
        try {
            $validated = $request->validate([
                'order_id' => 'required|exists:orders,order_id',
                'payment_method_id' => 'required|exists:payment_methods,payment_method_id',
                'amount' => 'required|numeric|min:0',
                'confirmation_date' => 'required|date',
                'status' => 'required|in:pending,approved,rejected',
                'proof_image' => 'nullable|string|max:255',
            ]);

            $confirmation = PaymentConfirmation::create($validated);

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
            $confirmation = PaymentConfirmation::with(['order', 'paymentMethod'])->findOrFail($id);

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
            $confirmation = PaymentConfirmation::findOrFail($id);

            $validated = $request->validate([
                'order_id' => 'sometimes|exists:orders,order_id',
                'payment_method_id' => 'sometimes|exists:payment_methods,payment_method_id',
                'amount' => 'sometimes|numeric|min:0',
                'confirmation_date' => 'sometimes|date',
                'status' => 'sometimes|in:pending,approved,rejected',
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
            $confirmation = PaymentConfirmation::findOrFail($id);
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

    private function errorResponse($e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Server error',
            'error' => config('app.debug') ? $e->getMessage() : null,
        ], 500);
    }
}
