<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = Order::with(['receipts.product', 'user']);

        $userId = $request['user_id'];

        $adminRoleId = Role::where('title', 'admin')->value('id');

        $userRoleId = User::with('role')->find($userId);

        if($userRoleId->role_id !== $adminRoleId){
            $orders->where('user_id', $request->query('user_id'));
        }

        $buf = $orders->get();

        if($buf->isEmpty()){
            return response()->json([
                'message' => 'no orders yet'
            ], 404);
        }

        return response()->json($buf);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $request->validate([
                'cart' => 'required|array|min:1',
                'cart.*.product_id' => 'required|integer|distinct|exists:products,id,stock,>0',
                'cart.*.amount' => 'required|integer|min:1',
        ]);

        $order = Order::create(['user_id' => $request['user_id']]);

        $receipt = $order->receipts()->createMany($validation['cart']);

        return response()->json([
            'message' => 'you have placed the order successfully',
            'receipt' => $receipt->load('product')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id)
    {
        $validated = $request->validate([
                'order_user_id'     => 'required|integer|exists:users,id',
                'cart'              => 'required|array|min:1',
                'cart.*.product_id' => 'required|integer|distinct|exists:products,id',
                'cart.*.amount'     => 'required|integer|min:1',
                'estimated_time'    => 'nullable|date',
                'arrived_at'        => 'nullable|date',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'user_id'        => $validated['order_user_id'],
            'estimated_time' => $validated['estimated_time'] ?? null,
            'arrived_at'     => $validated['arrived_at'] ?? null,
        ]);

        $order->receipts()->delete();
        $order->receipts()->createMany($validated['cart']);

        return response()->json([
            'message' => 'You have updated the order successfully',
            'data'    => $order->load('user', 'receipts.product'),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id)
    {
        $order = Order::findOrFail($id);
        $order->receipts()->delete();
        $order->delete();

        return response()->json([
            'message' => 'You have deleted the order successfully',
        ], 200);
    }
}
