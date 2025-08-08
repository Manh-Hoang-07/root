<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Product\ProductService;
use App\Http\Resources\Admin\Product\ProductResource;
use App\Http\Requests\Admin\Product\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    // Định nghĩa relations cho từng context
    protected $relations = [
        'index' => ['brand:id,name', 'categories:id,name'],
        'show' => [
            'brand:id,name', 
            'categories:id,name', 
            'variants:id,product_id,name,sku,price,sale_price,quantity,image,status',
            'variants.attributeValues:id,variant_id,attribute_id,value',
            'variants.attributeValues.attribute:id,name',
            'images:id,imageable_id,url'
        ],
        'select' => ['brand:id,name'],
        'search' => ['brand:id,name']
    ];

    public function __construct(ProductService $service)
    {
        parent::__construct($service, ProductResource::class);
        $this->storeRequestClass = ProductRequest::class;
        $this->updateRequestClass = ProductRequest::class;
    }

    /**
     * Override index method để tự control relations
     */
    public function index(Request $request)
    {
        // Parse relations từ request
        $requestRelations = $this->parseRelations($request->get('relations'));
        
        // Nếu có relations được yêu cầu thì dùng, không thì dùng default
        $relations = !empty($requestRelations) 
            ? $requestRelations 
            : $this->relations['index'];
        
        $fields = $this->parseFields($request->get('fields'));
        $data = $this->service->list($request->all(), $request->get('per_page', 20), $relations, $fields);
        
        return $this->listResource::collection($data);
    }

    /**
     * Override show method để tự control relations
     */
    public function show($id, Request $request = null)
    {
        // Parse relations từ request
        $requestRelations = $request ? $this->parseRelations($request->get('relations')) : [];
        
        // Nếu có relations được yêu cầu thì dùng, không thì dùng default
        $relations = !empty($requestRelations) 
            ? $requestRelations 
            : $this->relations['show'];
        
        $fields = $request ? $this->parseFields($request->get('fields')) : ['*'];
        $item = $this->service->find($id, $relations, $fields);
        
        return new $this->resource($item);
    }

    /**
     * Store a new product
     */
    public function store()
    {
        $request = app($this->getStoreRequestClass());
        $product = $this->service->createProduct($request->validated());
        
        // Load minimal relations cho response
        $product->load(['brand:id,name']);
        
        return new ProductResource($product);
    }

    /**
     * Update an existing product
     */
    public function update($id)
    {
        $request = app($this->getUpdateRequestClass());
        $product = $this->service->updateProduct($id, $request->validated());
        
        // Load minimal relations cho response
        $product->load(['brand:id,name']);
        
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

    /**
     * Search products với relations tối ưu
     */
    public function search(Request $request)
    {
        $filters = [];
        
        if ($request->get('search')) {
            $filters['search'] = $request->get('search');
        }
        
        if ($request->get('brand_id')) {
            $filters['brand_id'] = $request->get('brand_id');
        }
        
        if ($request->get('category_id')) {
            $filters['category_id'] = $request->get('category_id');
        }
        
        $limit = min($request->get('limit', 10), 100);
        $fields = $request->get('fields', ['id', 'name', 'sku']);
        
        // Dùng relations tối ưu cho search
        $relations = $this->relations['search'];
        
        $results = $this->service->list($filters, $limit, $relations, $fields);
        
        // Format cho select dropdown
        $data = $results->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
                'sku' => $item->sku ?? null,
                'brand' => $item->brand ? $item->brand->name : null
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Parse relations từ string thành array
     */
    protected function parseRelations($relations)
    {
        if (is_array($relations)) return $relations;
        if (is_string($relations)) {
            return array_filter(array_map('trim', explode(',', $relations)));
        }
        return [];
    }

    /**
     * Parse fields từ string thành array
     */
    protected function parseFields($fields)
    {
        if (is_array($fields)) return $fields;
        if (is_string($fields)) {
            return array_filter(array_map('trim', explode(',', $fields)));
        }
        return ['*'];
    }
} 
