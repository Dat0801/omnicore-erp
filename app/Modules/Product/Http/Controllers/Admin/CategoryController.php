<?php

namespace App\Modules\Product\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductCategory;
use Illuminate\Http\Request;
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
            ->orderBy('name')
            ->paginate(5)
            ->withQueryString()
            ->through(function (ProductCategory $category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'products_count' => $category->products_count,
                    'is_active' => $category->products_count > 0,
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
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}
