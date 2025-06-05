<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            $page = $request->get('page', 1);

            $users = User::paginate($limit, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'totalData' => $users->total(),
                'page' => $users->currentPage(),
                'limit' => $users->perPage()
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role'     => 'required|in:admin,user',
            ]);

            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
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
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $user,
                'totalData' => 1,
                'page' => 1,
                'limit' => 1
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validasi input
            $validated = $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'required|email|unique:users,email,' . $id . ',user_id',
                'oldPassword'  => 'nullable|required_with:newPassword', // harus ada jika newPassword disediakan
                'newPassword'  => 'nullable|min:6',
                'role'         => 'required|in:admin,user',
            ]);

            // Jika newPassword diisi, cek oldPassword
            if ($request->filled('newPassword')) {
                if (!Hash::check($request->oldPassword, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Old password is incorrect.'
                    ], 422);
                }

                // Update password dengan hash baru
                $validated['password'] = Hash::make($request->newPassword);
            }

            // Hapus field yang tidak diperlukan sebelum update
            unset($validated['oldPassword'], $validated['newPassword']);

            // Lakukan update data user
            $user->update($validated);

            return response()->json([
                'success' => true,
                'data' => $user,
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
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

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
                'message' => 'User not found.'
            ], 404);
        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    private function errorResponse($e)
    {
        return response()->json([
            'success' => false,
            'message' => 'Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}
