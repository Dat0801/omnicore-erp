<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository
    ) {}

    public function listProducts(array $filters = [], bool $paginate = true): LengthAwarePaginator|iterable
    {
        if ($paginate) {
            return $this->repository->getPaginated($filters);
        }
        return $this->repository->getAll();
    }

    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $productData = collect($data)->except(['images'])->toArray();
            $product = $this->repository->create($productData);

            if (isset($data['images']) && is_array($data['images'])) {
                $this->syncImages($product, $data['images']);
            }

            return $product;
        });
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $productData = collect($data)->except(['images'])->toArray();
            $product = $this->repository->update($product, $productData);

            if (isset($data['images']) && is_array($data['images'])) {
                // For simplicity, we might just append or replace. 
                // Let's assume we replace or add new ones. 
                // A better approach for update is often separate endpoints for images, 
                // but for a CRUD update, let's handle new files.
                $this->syncImages($product, $data['images']);
            }

            return $product;
        });
    }

    public function getProduct(int $id): ?Product
    {
        return $this->repository->findById($id);
    }

    public function deleteProduct(Product $product): Product
    {
        // "Product can be inactive but not deleted"
        return $this->repository->update($product, ['is_active' => false]);
    }

    protected function syncImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'url' => Storage::url($path),
                    'is_primary' => false, // Default to false
                ]);
            }
        }
    }
}
