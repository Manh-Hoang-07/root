<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends BaseController
{
    public function __construct(OrderService $orderService)
    {
        parent::__construct($orderService, OrderResource::class);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $order = \App\Models\Order::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $data['status'];
        $order->save();
        // Lưu lịch sử trạng thái
        \App\Models\OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $data['status'],
            'changed_at' => now(),
            'changed_by' => auth()->id() ?? 'system',
            'note' => $data['note'] ?? null,
        ]);
        return new \App\Http\Resources\OrderResource($order);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search', 'status', 'user_id', 'date_from', 'date_to']);
        $orders = $this->service->filter($filters, $perPage);
        return \App\Http\Resources\OrderResource::collection($orders);
    }

    public function show($id)
    {
        $order = $this->service->find($id);
        return new \App\Http\Resources\OrderResource($order);
    }

    public function orderItems($orderId)
    {
        $items = \App\Models\OrderItem::where('order_id', $orderId)->get();
        return \App\Http\Resources\OrderItemResource::collection($items);
    }
} 