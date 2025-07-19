<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\PromotionResource;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends BaseController
{
    public function __construct(PromotionService $promotionService)
    {
        parent::__construct($promotionService, PromotionResource::class);
    }

    public function assignProducts(Request $request, $promotionId)
    {
        $data = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
        ]);
        $promotion = \App\Models\Promotion::findOrFail($promotionId);
        $promotion->products()->sync($data['product_ids']);
        return response()->json(['message' => 'Gán sản phẩm vào chương trình khuyến mãi thành công']);
    }
} 