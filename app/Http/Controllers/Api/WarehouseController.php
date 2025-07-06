<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Warehouse::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by province
        if ($request->has('province')) {
            $query->where('province', $request->province);
        }

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }

        $warehouses = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $warehouses,
            'message' => 'Warehouses retrieved successfully'
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
    public function show(Warehouse $warehouse)
    {
        $warehouse->load(['products']);

        return response()->json([
            'success' => true,
            'data' => $warehouse,
            'message' => 'Warehouse retrieved successfully'
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
     * Get products in warehouse
     */
    public function products(Warehouse $warehouse, Request $request)
    {
        $query = $warehouse->products()->with(['inventory']);

        // Filter by product status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by available quantity
        if ($request->has('available')) {
            $query->whereHas('inventory', function ($q) use ($warehouse) {
                $q->where('warehouse_id', $warehouse->id)
                  ->where('available_quantity', '>', 0);
            });
        }

        $products = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'warehouse' => $warehouse,
                'products' => $products
            ],
            'message' => 'Warehouse products retrieved successfully'
        ]);
    }

    /**
     * Get warehouse statistics
     */
    public function stats(Warehouse $warehouse)
    {
        $totalProducts = $warehouse->products()->count();
        $activeProducts = $warehouse->products()->where('status', 'active')->count();
        $totalInventory = $warehouse->inventory()->sum('quantity');
        $availableInventory = $warehouse->inventory()->sum('available_quantity');
        $totalOrders = $warehouse->orders()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'warehouse' => $warehouse,
                'stats' => [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'total_inventory' => $totalInventory,
                    'available_inventory' => $availableInventory,
                    'total_orders' => $totalOrders,
                ]
            ],
            'message' => 'Warehouse statistics retrieved successfully'
        ]);
    }
}
