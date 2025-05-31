<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);

            $methods = PaymentMethod::paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $methods->items(),
                'totalData' => $methods->total(),
                'page' => $methods->currentPage(),
                'limit' => $methods->perPage()
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'method_name' => 'required|string|max:100',
                'details'     => 'nullable|string',
            ]);

            $method = PaymentMethod::create($validated);

            return response()->json([
                'success' => true,
                'data' => $method,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function show($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $method,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);

            $validated = $request->validate([
                'method_name' => 'required|string|max:100',
                'details'     => 'nullable|string',
            ]);

            $method->update($validated);

            return response()->json([
                'success' => true,
                'data' => $method,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function destroy($id)
    {
        try {
            $method = PaymentMethod::findOrFail($id);
            $method->delete();

            return response()->json([
                'success' => true,
                'data' => null,
                'totalData' => 0,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found'
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
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}
