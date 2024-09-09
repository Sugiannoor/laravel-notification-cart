<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Notification;

class CheckoutController extends Controller
{
    public function checkout (Request $request)
    {
        $user = Auth::user();
        $cart = $user->carts()->latest()->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['error' => 'Cart is empty'], 400);
        }

        // Hitung total
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        // Buat order
        $order = $user->orders()->create([
            'total' => $total,
            'status' => 'pending',
        ]);

        // Buat order items
        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
            ]);

            // Kurangi stock produk
            $item->product->decrement('stock', $item->quantity);
        }

        // Kirim notifikasi ke admin
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            $admin->notifications()->create([
                'message' => "User {$user->name} telah melakukan checkout dengan Order ID: {$order->id}.",
            ]);
        }

        // Kosongkan cart
        $cart->items()->delete();
        $cart->delete();

        return response()->json(['success' => 'Order telah berhasil dibuat.'], 201);
    }
}
