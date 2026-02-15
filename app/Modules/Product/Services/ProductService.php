<?php

namespace App\Modules\Product\Services;

use App\Modules\Inventory\Models\Inventory;
use App\Modules\Inventory\Models\Warehouse;
use App\Modules\Inventory\Services\InventoryService;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository,
        protected InventoryService $inventoryService
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
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = array_filter(array_map('trim', explode(',', $data['tags'])));
            }

            $productData = collect($data)->except(['images', 'quantity', 'low_stock_threshold', 'variants'])->toArray();
            $product = $this->repository->create($productData);
            $product->refresh();

            if (isset($data['images']) && is_array($data['images'])) {
                $this->syncImages($product, $data['images']);
            }

            // Handle Variants
            if (! empty($data['has_variants']) && ! empty($data['variants'])) {
                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product, $variantData);
                }
            } else {
                // Handle Inventory for single product
                $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;
                $lowStockThreshold = isset($data['low_stock_threshold']) ? (int) $data['low_stock_threshold'] : 0;
                $this->handleInventory($product, $quantity, $lowStockThreshold);
            }

            return $product;
        });
    }

    protected function createVariant(Product $parent, array $data): Product
    {
        $data['parent_id'] = $parent->id;
        // Generate name if not provided
        if (empty($data['name'])) {
            $data['name'] = $parent->name.' - '.implode(' ', array_values($data['variant_attributes']));
        }
        $data['type'] = $parent->type;
        $data['product_category_id'] = $parent->product_category_id;
        $data['unit'] = $parent->unit;

        // Ensure variant attributes are set
        if (! isset($data['variant_attributes'])) {
            $data['variant_attributes'] = [];
        }

        $variant = $this->repository->create($data);

        // Handle inventory for variant
        $quantity = isset($data['quantity']) ? (int) $data['quantity'] : 0;
        $lowStockThreshold = isset($data['low_stock_threshold']) ? (int) $data['low_stock_threshold'] : 0;

        $this->handleInventory($variant, $quantity, $lowStockThreshold);

        return $variant;
    }

    protected function handleInventory(Product $product, int $quantity, int $lowStockThreshold): void
    {
        if ($quantity > 0 || $lowStockThreshold > 0) {
            // Get default warehouse or create one
            $warehouse = Warehouse::firstOrCreate(
                ['code' => 'DEFAULT'],
                ['name' => 'Main Warehouse', 'is_active' => true]
            );

            if ($quantity > 0) {
                $this->inventoryService->addStock(
                    $warehouse->id,
                    $product->id,
                    $quantity,
                    'Initial Stock',
                    auth()->id()
                );
            }

            if ($lowStockThreshold > 0) {
                Inventory::updateOrCreate(
                    ['warehouse_id' => $warehouse->id, 'product_id' => $product->id],
                    ['reorder_level' => $lowStockThreshold]
                );
            }
        }
    }

    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if (isset($data['tags']) && is_string($data['tags'])) {
                $data['tags'] = array_filter(array_map('trim', explode(',', $data['tags'])));
            }

            $productData = collect($data)->except(['images', 'quantity', 'low_stock_threshold', 'variants'])->toArray();
            $product = $this->repository->update($product, $productData);

            if (isset($data['images']) && is_array($data['images'])) {
                $this->syncImages($product, $data['images']);
            }

            // Handle Low Stock Threshold Update
            if (isset($data['low_stock_threshold'])) {
                $warehouse = Warehouse::where('code', 'DEFAULT')->first();
                if ($warehouse) {
                    Inventory::updateOrCreate(
                        ['warehouse_id' => $warehouse->id, 'product_id' => $product->id],
                        ['reorder_level' => (int) $data['low_stock_threshold']]
                    );
                }
            }

            // Handle Variants
            if (isset($data['has_variants']) && $data['has_variants']) {
                if (isset($data['variants']) && is_array($data['variants'])) {
                    $this->syncVariants($product, $data['variants']);
                }
            }

            return $product;
        });
    }

    protected function syncVariants(Product $parent, array $variantsData): void
    {
        $existingIds = $parent->variants()->pluck('id')->toArray();
        $processedIds = [];

        foreach ($variantsData as $variantData) {
            if (isset($variantData['id']) && in_array($variantData['id'], $existingIds)) {
                // Update existing variant
                $variant = $this->repository->findById($variantData['id']);
                $updateData = collect($variantData)->only(['sku', 'price', 'variant_attributes', 'is_active'])->toArray();

                // Update name if attributes changed
                if (isset($updateData['variant_attributes'])) {
                    $updateData['name'] = $parent->name.' - '.implode(' ', array_values($updateData['variant_attributes']));
                }

                $this->repository->update($variant, $updateData);
                $processedIds[] = $variant->id;

                // Handle inventory for variant if provided (only reorder level for now)
                if (isset($variantData['low_stock_threshold'])) {
                    $warehouse = Warehouse::where('code', 'DEFAULT')->first();
                    if ($warehouse) {
                        Inventory::updateOrCreate(
                            ['warehouse_id' => $warehouse->id, 'product_id' => $variant->id],
                            ['reorder_level' => (int) $variantData['low_stock_threshold']]
                        );
                    }
                }
            } else {
                // Create new variant
                $this->createVariant($parent, $variantData);
            }
        }

        // Delete variants that were removed
        $toDelete = array_diff($existingIds, $processedIds);
        if (! empty($toDelete)) {
            Product::whereIn('id', $toDelete)->delete();
        }
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
