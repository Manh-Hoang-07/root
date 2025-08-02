<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Variant;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable cache for testing
        Cache::flush();
    }

    /** @test */
    public function it_avoids_n_plus_one_queries_in_product_resource()
    {
        // Create test data
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();
        $attribute = Attribute::factory()->create();
        
        $product = Product::factory()->create([
            'brand_id' => $brand->id,
        ]);
        
        $product->categories()->attach($category->id);
        
        $variant = Variant::factory()->create([
            'product_id' => $product->id,
        ]);
        
        $attributeValue = AttributeValue::factory()->create([
            'attribute_id' => $attribute->id,
        ]);
        
        $variant->attributeValues()->attach($attributeValue->id);
        
        // Enable query log
        DB::enableQueryLog();
        
        // Test with eager loading (should be optimized)
        $productWithRelations = Product::with([
            'brand',
            'categories', 
            'variants.attributeValues.attribute'
        ])->find($product->id);
        
        $queryCount = count(DB::getQueryLog());
        
        // Should be 4 queries: 1 for product, 1 for brand, 1 for categories, 1 for variants with attributeValues and attributes
        $this->assertLessThanOrEqual(4, $queryCount, "Expected <= 4 queries, got {$queryCount}");
        
        // Verify data is loaded correctly
        $this->assertNotNull($productWithRelations->brand);
        $this->assertNotNull($productWithRelations->categories->first());
        $this->assertNotNull($productWithRelations->variants->first());
        $this->assertNotNull($productWithRelations->variants->first()->attributeValues->first());
        $this->assertNotNull($productWithRelations->variants->first()->attributeValues->first()->attribute);
    }

    /** @test */
    public function it_caches_api_responses()
    {
        $product = Product::factory()->create();
        
        // First request should hit database
        $response1 = $this->getJson("/api/admin/products/{$product->id}");
        $response1->assertStatus(200);
        
        // Second request should be cached
        $response2 = $this->getJson("/api/admin/products/{$product->id}");
        $response2->assertStatus(200);
        
        // Both responses should be identical
        $this->assertEquals($response1->json(), $response2->json());
        
        // Verify cache key exists
        $cacheKey = "api_App\\Http\\Controllers\\Api\\Admin\\Product\\ProductController_show_{$product->id}_" . md5(serialize([]));
        $this->assertTrue(Cache::has($cacheKey));
    }

    /** @test */
    public function it_invalidates_cache_on_model_update()
    {
        $product = Product::factory()->create();
        
        // First request to cache the response
        $this->getJson("/api/admin/products/{$product->id}");
        
        // Verify cache exists
        $cacheKey = "api_App\\Http\\Controllers\\Api\\Admin\\Product\\ProductController_show_{$product->id}_" . md5(serialize([]));
        $this->assertTrue(Cache::has($cacheKey));
        
        // Update the product
        $this->putJson("/api/admin/products/{$product->id}", [
            'name' => 'Updated Product Name'
        ]);
        
        // Cache should be invalidated
        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function it_invalidates_cache_on_model_deletion()
    {
        $product = Product::factory()->create();
        
        // First request to cache the response
        $this->getJson("/api/admin/products/{$product->id}");
        
        // Verify cache exists
        $cacheKey = "api_App\\Http\\Controllers\\Api\\Admin\\Product\\ProductController_show_{$product->id}_" . md5(serialize([]));
        $this->assertTrue(Cache::has($cacheKey));
        
        // Delete the product
        $this->deleteJson("/api/admin/products/{$product->id}");
        
        // Cache should be invalidated
        $this->assertFalse(Cache::has($cacheKey));
    }

    /** @test */
    public function it_optimizes_relations_with_select_fields()
    {
        $brand = Brand::factory()->create();
        $product = Product::factory()->create(['brand_id' => $brand->id]);
        
        DB::enableQueryLog();
        
        // Test with optimized relations
        $product = Product::with('brand:id,name')->find($product->id);
        
        $queryLog = DB::getQueryLog();
        
        // Should have 2 queries: 1 for product, 1 for brand with only id and name
        $this->assertCount(2, $queryLog);
        
        // Verify brand query only selects id and name
        $brandQuery = $queryLog[1]['sql'];
        $this->assertStringContainsString('select `id`, `name`', strtolower($brandQuery));
    }

    /** @test */
    public function it_handles_field_selection_correctly()
    {
        $product = Product::factory()->create();
        
        // Test with specific fields
        $response = $this->getJson("/api/admin/products?fields=id,name,price");
        
        $response->assertStatus(200);
        
        $data = $response->json()['data'][0];
        
        // Should only contain requested fields
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('price', $data);
        $this->assertArrayNotHasKey('description', $data);
    }

    /** @test */
    public function it_handles_relations_parameter_correctly()
    {
        $brand = Brand::factory()->create();
        $product = Product::factory()->create(['brand_id' => $brand->id]);
        
        // Test with relations parameter
        $response = $this->getJson("/api/admin/products?relations=brand");
        
        $response->assertStatus(200);
        
        $data = $response->json()['data'][0];
        
        // Should include brand relation
        $this->assertArrayHasKey('brand', $data);
        $this->assertNotNull($data['brand']);
    }

    /** @test */
    public function it_logs_performance_metrics()
    {
        $product = Product::factory()->create();
        
        $response = $this->getJson("/api/admin/products/{$product->id}");
        
        $response->assertStatus(200);
        
        // Check for performance headers
        $this->assertTrue($response->headers->has('X-Execution-Time'));
        $this->assertTrue($response->headers->has('X-Memory-Usage'));
        
        // Verify execution time is reasonable
        $executionTime = (float) str_replace('ms', '', $response->headers->get('X-Execution-Time'));
        $this->assertLessThan(1000, $executionTime, 'Execution time should be less than 1 second');
    }
} 