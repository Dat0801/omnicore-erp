<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'total_amount',
        'status',
        'source',
        'source_id',
        'customer_email',
        'customer_name',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
