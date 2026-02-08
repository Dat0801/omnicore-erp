<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status']);
        $products = $this->service->listProducts($filters);
        
        // Calculate stats
        $activeCount = Product::where('is_active', true)->count();
        
        // For out of stock, we consider products that have 0 total quantity across all warehouses
        // This is a simplified check.
        $productsWithStock = Inventory::select('product_id')
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) > 0')
            ->pluck('product_id');
            
        $outOfStockCount = Product::whereNotIn('id', $productsWithStock)->count();
        
        $topCategory = ProductCategory::withCount('products')
            ->orderByDesc('products_count')
            ->first();
            
        $recentUpdates = Product::where('updated_at', '>=', now()->startOfDay())->count();

        return Inertia::render('Product/Index', [
            'products' => $products,
            'filters' => $filters,
            'stats' => [
                'activeCount' => $activeCount,
                'outOfStockCount' => $outOfStockCount,
                'topCategory' => $topCategory ? $topCategory->name : 'N/A',
                'topCategoryCount' => $topCategory ? $topCategory->products_count : 0,
                'recentUpdates' => $recentUpdates,
                'totalProducts' => Product::count(),
            ]
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Product/Create', [
            'categories' => ProductCategory::all(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->service->createProduct($request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Product/Edit', [
            'product' => $product->load(['category', 'images']),
            'categories' => ProductCategory::all(),
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->service->updateProduct($product, $request->validated());

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->service->deleteProduct($product);
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deactivated successfully.');
    }
}
