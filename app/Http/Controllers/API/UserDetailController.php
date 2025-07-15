<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use Illuminate\Http\Request;

class UserDetailController extends Controller
{
    public function index()
    {
        return response()->json(UserDetail::with('user')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,user_id|unique:user_details,user_id',
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $detail = UserDetail::create($validated);

        return response()->json($detail, 201);
    }

    public function show($id)
    {
        $detail = UserDetail::with('user')->findOrFail($id);
        return response()->json($detail);
    }

    public function update(Request $request, $id)
    {
        $detail = UserDetail::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'address' => 'sometimes|required|string',
            'phone_number' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $detail->update($validated);

        return response()->json($detail);
    }

    public function destroy($id)
    {
        $detail = UserDetail::findOrFail($id);
        $detail->delete();

        return response()->json(['message' => 'User detail deleted successfully']);
    }
}
