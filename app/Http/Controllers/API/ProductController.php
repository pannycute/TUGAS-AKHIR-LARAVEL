<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);

            $products = Product::paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'totalData' => $products->total(),
                'page' => $products->currentPage(),
                'limit' => $products->perPage()
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
            ]);

            $product = Product::create($validated);

            return response()->json([
                'success' => true,
                'data' => $product,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $product,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e, 404, 'Product not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
            ]);

            $product->update($validated);

            return response()->json([
                'success' => true,
                'data' => $product,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return $this->errorResponse($e, 404, 'Product not found.');
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'totalData' => 0,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e, 404, 'Product not found.');
        }
    }

    private function errorResponse($e, $code = 500, $customMessage = 'Server Error')
    {
        return response()->json([
            'success' => false,
            'message' => $customMessage,
            'error' => config('app.debug') ? $e->getMessage() : null
        ], $code);
    }
}

