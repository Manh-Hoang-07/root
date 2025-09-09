<?php
namespace App\Http\Controllers\Api\Admin\Product;

use App\Http\Controllers\Api\BaseController;
use App\Services\Product\ProductService;
use App\Http\Requests\Admin\Product\ProductRequest;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    // Tối ưu relations - chỉ load những gì cần thiết
    protected $relations = [
        'index' => ['brand:id,name'], // Giảm từ 2 xuống 1 relation
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
        parent::__construct($service);
        $this->storeRequestClass = ProductRequest::class;
        $this->updateRequestClass = ProductRequest::class;
    }

    /**
     * Override index method với tối ưu hiệu suất
     */
    public function index(Request $request)
    {
        // Parse relations từ request
        $requestRelations = $this->parseRelations($request->get('relations'));
        
        // Nếu có relations được yêu cầu thì dùng, không thì dùng default tối ưu
        $relations = !empty($requestRelations) 
            ? $requestRelations 
            : $this->relations['index'];
        
        $fields = $this->parseFields($request->get('fields'));
        
        // Tối ưu: Chỉ load fields cần thiết cho list
        if (empty($fields) || $fields === ['*']) {
            $fields = ['id', 'name', 'slug', 'price', 'sale_price', 'image', 'brand_id', 'status', 'created_at'];
        }
        
        $data = $this->service->list($request->all(), $request->get('per_page', 20), $relations, $fields);
        
        return $this->successResponse(
            $this->formatCollectionData($data),
            'Lấy danh sách sản phẩm thành công'
        );
    }

    /**
     * Override show method với tối ưu hiệu suất
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
        
        return $this->successResponse(
            $this->formatSingleData($item),
            'Lấy thông tin sản phẩm thành công'
        );
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
        
        return $this->successResponse(
            $this->formatSingleData($product),
            'Tạo sản phẩm thành công'
        );
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
        
        return $this->successResponse(
            $this->formatSingleData($product),
            'Cập nhật sản phẩm thành công'
        );
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
    protected function parseRelations($relations): array
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
    protected function parseFields($fields): array
    {
        if (is_array($fields)) return $fields;
        if (is_string($fields)) {
            return array_filter(array_map('trim', explode(',', $fields)));
        }
        return ['*'];
    }
} 
