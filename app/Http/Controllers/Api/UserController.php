<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by role (if you have roles)
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')
                      ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully'
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
    public function show(User $user)
    {
        $user->load(['orders']);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User retrieved successfully'
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
     * Get user orders
     */
    public function orders(User $user, Request $request)
    {
        $query = $user->orders()->with(['warehouse', 'items.product']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->orderBy('created_at', 'desc')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'orders' => $orders
            ],
            'message' => 'User orders retrieved successfully'
        ]);
    }

    /**
     * Get user statistics
     */
    public function stats(User $user)
    {
        $totalOrders = $user->orders()->count();
        $totalSpent = $user->orders()->sum('final_amount');
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $deliveredOrders = $user->orders()->where('status', 'delivered')->count();
        $cancelledOrders = $user->orders()->where('status', 'cancelled')->count();

        // Orders by status
        $ordersByStatus = $user->orders()
                              ->select('status', DB::raw('count(*) as count'))
                              ->groupBy('status')
                              ->get();

        // Orders by month (last 12 months)
        $ordersByMonth = $user->orders()
                             ->select(DB::raw('YEAR(created_at) as year'), DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as count'))
                             ->whereDate('created_at', '>=', now()->subMonths(12))
                             ->groupBy('year', 'month')
                             ->orderBy('year')
                             ->orderBy('month')
                             ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'stats' => [
                    'total_orders' => $totalOrders,
                    'total_spent' => $totalSpent,
                    'pending_orders' => $pendingOrders,
                    'delivered_orders' => $deliveredOrders,
                    'cancelled_orders' => $cancelledOrders,
                ],
                'orders_by_status' => $ordersByStatus,
                'orders_by_month' => $ordersByMonth,
            ],
            'message' => 'User statistics retrieved successfully'
        ]);
    }
}
