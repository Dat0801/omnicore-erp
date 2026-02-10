<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Http\Requests\StoreProductCategoryRequest;
use App\Modules\Product\Http\Requests\UpdateProductCategoryRequest;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->toString();

        $categories = ProductCategory::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->withCount('products')
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(function (ProductCategory $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'parent_name' => $category->parent ? $category->parent->name : '-',
                    'slug' => $category->slug,
                    'products_count' => $category->products_count,
                    'is_active' => $category->is_active,
                    'is_featured' => $category->is_featured,
                    'icon' => $category->icon,
                ];
            });

        return Inertia::render('Category/Index', [
            'categories' => $categories,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Category/Create', [
            'parentCategories' => ProductCategory::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    public function store(StoreProductCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        
        if ($request->hasFile('icon')) {
             $path = $request->file('icon')->store('categories', 'public');
             $data['icon'] = Storage::url($path);
        }

        ProductCategory::create($data);

        return to_route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(ProductCategory $category): Response
    {
        return Inertia::render('Category/Edit', [
            'category' => $category,
            'parentCategories' => ProductCategory::query()
                ->where('id', '!=', $category->id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']),
            'products' => $category->products()
                ->select('id', 'name', 'sku', 'price', 'is_active')
                ->limit(50)
                ->get(),
        ]);
    }

    public function assignProduct(Request $request, ProductCategory $category)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        $product->product_category_id = $category->id;
        $product->save();
        
        return back()->with('success', 'Product assigned to category.');
    }

    public function unassignProduct(ProductCategory $category, Product $product)
    {
        if ($product->product_category_id === $category->id) {
            $product->product_category_id = null;
            $product->save();
        }
        
        return back()->with('success', 'Product removed from category.');
    }

    public function update(UpdateProductCategoryRequest $request, ProductCategory $category)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                $oldPath = str_replace('/storage/', '', $category->icon);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('icon')->store('categories', 'public');
            $data['icon'] = Storage::url($path);
        }

        $category->update($data);

        return to_route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ProductCategory $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Cannot delete category with associated products.');
        }

        if ($category->children()->exists()) {
            return back()->with('error', 'Cannot delete category with sub-categories.');
        }

        if ($category->icon) {
            $oldPath = str_replace('/storage/', '', $category->icon);
            Storage::disk('public')->delete($oldPath);
        }

        $category->delete();

        return to_route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
