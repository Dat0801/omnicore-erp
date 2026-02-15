<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\Models\Inventory;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function search(Request $request)
    {
        $query = $request->input('query');

        $products = Product::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->limit(20)
            ->get(['id', 'name', 'sku', 'price', 'product_category_id']);

        return response()->json($products);
    }

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
            ],
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

    public function show(Product $product): Response
    {
        $product->load(['category', 'images', 'variants' => function ($query) {
            $query->withSum('inventories', 'quantity');
        }, 'variants.images']);

        $product->loadSum('inventories', 'quantity');

        return Inertia::render('Product/Show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): Response
    {
        $product->load(['category', 'images', 'variants']);

        // Load inventory data for default warehouse
        $inventory = Inventory::where('product_id', $product->id)
            ->whereHas('warehouse', function ($q) {
                $q->where('code', 'DEFAULT');
            })->first();

        $product->quantity = $inventory ? $inventory->quantity : 0;
        $product->low_stock_threshold = $inventory ? $inventory->reorder_level : 0;

        // Load inventory for variants if they exist
        if ($product->has_variants) {
            foreach ($product->variants as $variant) {
                $variantInventory = Inventory::where('product_id', $variant->id)
                    ->whereHas('warehouse', function ($q) {
                        $q->where('code', 'DEFAULT');
                    })->first();
                $variant->quantity = $variantInventory ? $variantInventory->quantity : 0;
            }
        }

        return Inertia::render('Product/Edit', [
            'product' => $product,
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
