<?php

namespace App\Modules\Product\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Product\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index(): JsonResponse
    {
        // External systems usually fetch all or paginated
        $products = $this->service->listProducts(paginate: true);

        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->service->getProduct($id);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }
}
