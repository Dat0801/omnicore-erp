<?php

namespace App\Modules\Product\Services;

use App\Modules\Product\Repositories\ProductRepository;
use App\Modules\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository
    ) {}

    public function listProducts(bool $paginate = true): LengthAwarePaginator|iterable
    {
        if ($paginate) {
            return $this->repository->getPaginated();
        }
        return $this->repository->getAll();
    }

    public function createProduct(array $data): Product
    {
        // Business logic: e.g. validate SKU uniqueness if not handled by Request, 
        // or trigger events for external systems.
        return $this->repository->create($data);
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return $this->repository->update($product, $data);
    }

    public function getProduct(int $id): ?Product
    {
        return $this->repository->findById($id);
    }
}
