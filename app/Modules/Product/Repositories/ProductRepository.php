<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function getAll(): Collection
    {
        return Product::with(['category', 'images'])->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with(['category', 'images'])->latest()->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::with(['category', 'images'])->find($id);
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
