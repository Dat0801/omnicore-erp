<?php

namespace App\Modules\Product\Repositories;

use App\Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function getAll(): Collection
    {
        return Product::all();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Product::latest()->paginate($perPage);
    }

    public function findById(int $id): ?Product
    {
        return Product::find($id);
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
        return $product->delete();
    }
}
