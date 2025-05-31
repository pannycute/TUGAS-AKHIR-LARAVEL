<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class OrderItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $items = OrderItem::with(['order', 'product'])->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $items->items(),
                'totalData' => $items->total(),
                'page' => $items->currentPage(),
                'limit' => $items->perPage()
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
                'product_id' => 'required|exists:products,product_id',
                'quantity' => 'required|integer|min:1',
                'unit_price' => 'required|numeric|min:0',
            ]);

            $validated['subtotal'] = $validated['quantity'] * $validated['unit_price'];

            $item = OrderItem::create([
                'order_id' => $validated['order_id'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $validated['unit_price'],
                'subtotal' => $validated['quantity'] * $validated['unit_price']
            ]);

            return response()->json([
                'success' => true,
                'data' => $item,
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
            $item = OrderItem::with(['order', 'product'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $item,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = OrderItem::findOrFail($id);

            $validated = $request->validate([
                'order_id' => 'sometimes|exists:orders,order_id',
                'product_id' => 'sometimes|exists:products,product_id',
                'quantity' => 'sometimes|integer|min:1',
                'unit_price' => 'sometimes|numeric|min:0',
            ]);

            if (isset($validated['quantity']) || isset($validated['unit_price'])) {
                $quantity = $validated['quantity'] ?? $item->quantity;
                $unit_price = $validated['unit_price'] ?? $item->unit_price;
                $validated['subtotal'] = $quantity * $unit_price;
            }

            $item->update($validated);

            return response()->json([
                'success' => true,
                'data' => $item,
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
                'message' => 'Order item not found',
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy($id)
    {
        try {
            $item = OrderItem::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order item deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order item not found',
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
