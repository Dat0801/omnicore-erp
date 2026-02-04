<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreProductRequest;
use App\Modules\Product\Http\Requests\UpdateProductRequest;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Services\ProductService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(): Response
    {
        $products = $this->service->listProducts();
        return Inertia::render('Product/Index', [
            'products' => $products
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
