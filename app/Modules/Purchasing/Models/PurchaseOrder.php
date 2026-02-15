<?php

namespace App\Modules\Purchasing\Models;

use App\Modules\Inventory\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'code',
        'status',
        'total_amount',
        'is_received',
        'received_at',
        'ordered_at',
        'expected_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'is_received' => 'boolean',
        'received_at' => 'datetime',
        'ordered_at' => 'datetime',
        'expected_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
