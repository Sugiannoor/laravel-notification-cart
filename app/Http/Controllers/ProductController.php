<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        // Menampilkan semua produk
        $products = Product::all();
        return response()->json([
            'data' => $products,
            'code' => 200,
            'status' => 'OK',
        ]);
    }

    public function store(Request $request)
    {
        // Validasi data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        // Buat produk baru
        $product = Product::create($validated);

        return response()->json([
            'message' => 'Product created successfully',
            'code' => 201,
            'product' => $product,],201);
    }

    public function show($id)
    {
        // Tampilkan produk berdasarkan id
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found',
            'code' => 404,
            'status' => 'Not Found'], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        // Validasi data
        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric',
            'stock' => 'integer',
        ]);

        // Update produk
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'code' => 200,
            'status' => 'OK',
        ],200);
    }

    public function destroy($id)
    {
        // Hapus produk
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'
    , 'code' => 200,
    'status' => 'OK'], 200);
    }
}
