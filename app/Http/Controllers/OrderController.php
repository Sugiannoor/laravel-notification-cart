<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = $user->orders()->with('items.product')->get();

        return response()->json([
            'data' => $orders,
            'code' => 200,
            'status' => 'OK',]);
    }

    public function show($id)
    {
        // Tampilkan detail order
        $user = Auth::user();
        $order = $user->orders()->with('items.product')->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'data' => $order,
            'code' => 200,
            'status' => 'OK',
        ]);
    }
}
