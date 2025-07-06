<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'warehouse', 'shippingZone', 'items.product']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by warehouse
        if ($request->has('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Search by order number
        if ($request->has('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully'
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
    public function show(Order $order)
    {
        $order->load([
            'user',
            'warehouse',
            'shippingZone',
            'items.product',
            'statusHistory.changedBy',
            'shippingInfo'
        ]);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order retrieved successfully'
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
     * Get order status history
     */
    public function statusHistory(Order $order)
    {
        $history = $order->statusHistory()->with('changedBy')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'history' => $history
            ],
            'message' => 'Order status history retrieved successfully'
        ]);
    }

    /**
     * Get order statistics
     */
    public function stats(Request $request)
    {
        $query = Order::query();

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('final_amount'),
            'pending_orders' => $query->where('status', 'pending')->count(),
            'confirmed_orders' => $query->where('status', 'confirmed')->count(),
            'shipping_orders' => $query->where('status', 'shipping')->count(),
            'delivered_orders' => $query->where('status', 'delivered')->count(),
            'cancelled_orders' => $query->where('status', 'cancelled')->count(),
            'paid_orders' => $query->where('payment_status', 'paid')->count(),
            'pending_payment' => $query->where('payment_status', 'pending')->count(),
        ];

        // Orders by status
        $ordersByStatus = $query->select('status', DB::raw('count(*) as count'))
                               ->groupBy('status')
                               ->get();

        // Orders by date (last 30 days)
        $ordersByDate = $query->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                             ->whereDate('created_at', '>=', now()->subDays(30))
                             ->groupBy('date')
                             ->orderBy('date')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'orders_by_status' => $ordersByStatus,
                'orders_by_date' => $ordersByDate,
            ],
            'message' => 'Order statistics retrieved successfully'
        ]);
    }
}
