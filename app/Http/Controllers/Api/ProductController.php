<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductInventory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['inventory.warehouse']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by featured
        if ($request->has('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by brand
        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['inventory.warehouse']);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product retrieved successfully'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get product inventory by warehouse
     */
    public function inventory(Product $product)
    {
        $inventory = $product->inventory()->with('warehouse')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'inventory' => $inventory,
                'total_quantity' => $product->total_quantity,
                'available_quantity' => $product->available_quantity,
            ],
            'message' => 'Product inventory retrieved successfully'
        ]);
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        $products = Product::where('featured', true)
                          ->where('status', 'active')
                          ->with(['inventory.warehouse'])
                          ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Featured products retrieved successfully'
        ]);
    }
}
