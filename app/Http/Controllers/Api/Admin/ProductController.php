<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct(ProductService $productService)
    {
        parent::__construct($productService, ProductResource::class);
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $filters = $request->only(['search', 'category_id', 'brand_id', 'status']);
        $products = $this->service->list($filters, $perPage);
        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'image' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
        ]);
        $product = $this->service->create($data);
        return new ProductResource($product);
    }

    public function show($id)
    {
        $product = $this->service->find($id);
        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,'.$id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'sale_price' => 'nullable|numeric',
            'image' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'sometimes|required|in:active,inactive',
        ]);
        $product = $this->service->update($id, $data);
        return new ProductResource($product);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Xóa sản phẩm thành công']);
    }

    public function variants($productId)
    {
        $variants = \App\Models\Variant::where('product_id', $productId)->get();
        return \App\Http\Resources\VariantResource::collection($variants);
    }

    public function storeVariant(Request $request, $productId)
    {
        $data = $request->validate([
            'sku' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'sale_price' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        $data['product_id'] = $productId;
        $variant = \App\Models\Variant::create($data);
        return new \App\Http\Resources\VariantResource($variant);
    }

    public function updateVariant(Request $request, $productId, $variantId)
    {
        $data = $request->validate([
            'sku' => 'sometimes|required|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'price' => 'sometimes|required|numeric',
            'sale_price' => 'nullable|numeric',
            'quantity' => 'sometimes|required|integer',
            'image' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
        ]);
        $variant = \App\Models\Variant::where('product_id', $productId)->findOrFail($variantId);
        $variant->update($data);
        return new \App\Http\Resources\VariantResource($variant);
    }

    public function destroyVariant($productId, $variantId)
    {
        $variant = \App\Models\Variant::where('product_id', $productId)->findOrFail($variantId);
        $variant->delete();
        return response()->json(['message' => 'Xóa biến thể thành công']);
    }

    public function gallery($productId)
    {
        $images = \App\Models\Image::where('product_id', $productId)->orderBy('sort_order')->get();
        return \App\Http\Resources\ImageResource::collection($images);
    }

    public function addGalleryImage(Request $request, $productId)
    {
        $data = $request->validate([
            'url' => 'required|string',
            'alt' => 'nullable|string',
            'sort_order' => 'nullable|integer',
        ]);
        $data['product_id'] = $productId;
        $image = \App\Models\Image::create($data);
        return new \App\Http\Resources\ImageResource($image);
    }

    public function deleteGalleryImage($productId, $imageId)
    {
        $image = \App\Models\Image::where('product_id', $productId)->findOrFail($imageId);
        $image->delete();
        return response()->json(['message' => 'Xóa ảnh gallery thành công']);
    }

    // Có thể bổ sung các API đặc thù cho sản phẩm ở đây nếu cần
} 