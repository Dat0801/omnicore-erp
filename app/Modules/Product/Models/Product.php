<?php

namespace App\Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Inventory\Models\Inventory;
use Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'parent_id',
        'name',
        'sku',
        'barcode',
        'type',
        'description',
        'price',
        'cost_price',
        'unit',
        'weight',
        'dimensions',
        'allow_backorders',
        'tags',
        'variant_attributes',
        'product_category_id',
        'is_active',
        'has_variants',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'has_variants' => 'boolean',
        'allow_backorders' => 'boolean',
        'tags' => 'array',
        'dimensions' => 'array',
        'variant_attributes' => 'array',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function variantsInventories(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Inventory::class, Product::class, 'parent_id', 'product_id');
    }
}
