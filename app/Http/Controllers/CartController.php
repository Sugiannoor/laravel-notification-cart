<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Menampilkan keranjang pengguna
        $user = Auth::user();
        $cart = $user->carts()->with('items.product')->latest()->first();

        if (!$cart) {
            return response()->json(['message' => 'Cart is empty'], 404);
        }

        return response()->json($cart);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $cart = $user->carts()->latest()->first();

        // Buat keranjang baru jika belum ada
        if (!$cart) {
            $cart = $user->carts()->create();
        }

        // Tambahkan item ke keranjang
        $cart->items()->create([
            'product_id' => $validated['product_id'],
            'quantity' => $validated['quantity'],
        ]);

        return response()->json($cart->load('items.product'));
    }

    public function update(Request $request, $id)
    {
        // Update item di keranjang
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->update($validated);

        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        // Hapus item dari keranjang
        $cartItem = CartItem::find($id);

        if (!$cartItem) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed successfully']);
    }
}
