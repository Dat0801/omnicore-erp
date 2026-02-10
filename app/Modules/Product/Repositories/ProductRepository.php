<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function getAll(): Collection
    {
        return Product::with(['category', 'images'])->get();
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::with(['category', 'images', 'variants'])
            ->withSum('inventories', 'quantity')
            ->withSum('variantsInventories', 'quantity')
            ->whereNull('parent_id') // Only show parent products
            ->latest();

        if (isset($filters['search']) && $filters['search']) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::with(['category', 'images', 'variants.images', 'variants.inventories'])->find($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        return $product;
    }

    public function delete(Product $product): bool
    {
        // Ideally this should not be called if soft delete is not allowed,
        // but if we ever need to hard delete, this is it.
        return $product->delete();
    }
}
