<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductService;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index()
    {
        $products = $this->service->listProducts();
        return Inertia::render('Product/Index', [
            'products' => $products
        ]);
    }

    public function create()
    {
        return Inertia::render('Product/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $this->service->createProduct($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Product/Edit', [
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $this->service->updateProduct($product, $validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }
}
