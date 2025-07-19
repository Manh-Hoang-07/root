<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ShippingInfoResource;
use App\Services\ShippingInfoService;
use Illuminate\Http\Request;

class ShippingInfoController extends BaseController
{
    public function __construct(ShippingInfoService $shippingInfoService)
    {
        parent::__construct($shippingInfoService, ShippingInfoResource::class);
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);
        $shippingInfo = \App\Models\ShippingInfo::findOrFail($id);
        $shippingInfo->status = $data['status'];
        $shippingInfo->save();
        // Lưu lịch sử vận chuyển
        \App\Models\OrderStatusHistory::create([
            'order_id' => $shippingInfo->order_id,
            'status' => $data['status'],
            'changed_at' => now(),
            'changed_by' => auth()->id() ?? 'system',
            'note' => $data['note'] ?? null,
        ]);
        return new \App\Http\Resources\ShippingInfoResource($shippingInfo);
    }

    public function history($id)
    {
        $shippingInfo = \App\Models\ShippingInfo::findOrFail($id);
        $history = \App\Models\OrderStatusHistory::where('order_id', $shippingInfo->order_id)
            ->orderByDesc('changed_at')->get();
        return response()->json(['history' => $history]);
    }
} 