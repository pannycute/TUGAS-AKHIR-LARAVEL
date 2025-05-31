<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $orders = Order::with('user')->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $orders->items(),
                'totalData' => $orders->total(),
                'page' => $orders->currentPage(),
                'limit' => $orders->perPage()
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'order_date' => 'required|date',
                'status' => 'required|in:pending,confirmed,shipped,completed,cancelled',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,product_id',
                'items.*.quantity' => 'required|integer|min:1',
            ]);

            // Buat order terlebih dahulu dengan total_amount = 0 (sementara)
            $order = Order::create([
                'user_id' => $validated['user_id'],
                'order_date' => $validated['order_date'],
                'status' => $validated['status'],
                'total_amount' => 0, // akan di-update setelah semua item dihitung
            ]);

            $totalAmount = 0;

            foreach ($validated['items'] as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $unitPrice = $product->price;
                $subtotal = $unitPrice * $itemData['quantity'];
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }

            // Update total_amount pada order
            $order->update(['total_amount' => $totalAmount]);

            return response()->json([
                'success' => true,
                'data' => $order->load('user', 'orderItems.product'),
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
            $order = Order::with([
                'user',
                'orderItems.product',
                'paymentConfirmations.paymentMethod'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $validated = $request->validate([
                'user_id' => 'required|exists:users,user_id',
                'order_date' => 'required|date',
                'status' => 'required|in:pending,confirmed,shipped,completed,cancelled',
                'total_amount' => 'required|numeric|min:0',
            ]);

            $order->update($validated);

            return response()->json([
                'success' => true,
                'data' => $order,
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
                'message' => 'Order not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy($id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
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
