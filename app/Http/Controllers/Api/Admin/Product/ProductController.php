<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\Admin\BaseController;
use App\Services\Product\ProductService;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Http\Requests\Admin\Product\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct(ProductService $service)
    {
        parent::__construct($service, ProductResource::class);
        $this->storeRequestClass = ProductRequest::class;
        $this->updateRequestClass = ProductRequest::class;
    }

    /**
     * Get products with filters and pagination
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'brand_id', 'category_id', 'sort_by', 'per_page']);
        $products = $this->service->getProductsWithRelations($filters);
        
        return ProductResource::collection($products);
    }

    /**
     * Store a new product
     */
    public function store()
    {
        $request = app($this->getStoreRequestClass());
        $product = $this->service->createProduct($request->validated());
        
        return new ProductResource($product);
    }

    /**
     * Update an existing product
     */
    public function update($id)
    {
        $request = app($this->getUpdateRequestClass());
        $product = $this->service->updateProduct($id, $request->validated());
        
        return new ProductResource($product);
    }

    /**
     * Get product with all relationships
     */
    public function show($id, Request $request = null)
    {
        $product = $this->service->getProductWithRelations($id);
        
        return new ProductResource($product);
    }

    /**
     * Get products for select dropdown
     */
    public function getForSelect()
    {
        $products = $this->service->getProductsForSelect();
        
        return response()->json($products);
    }
} 
