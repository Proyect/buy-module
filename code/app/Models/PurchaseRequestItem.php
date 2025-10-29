<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestItem extends Model
{
    protected $table = 'purchase_request_items';

    protected $fillable = [
        'purchase_request_id',
        'product_id',
        'supplier_id',
        'quantity',
        'unit_price',
        'total_price',
        'description',
        'specifications',
        'required_date',
        'status',
        'is_custom',
        'custom_name',
        'comments',
    ];

    protected $casts = [
        'required_date' => 'date',
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_custom' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (PurchaseRequestItem $item) {
            $quantity = (int) ($item->quantity ?? 0);
            $unit = (float) ($item->unit_price ?? 0);
            $item->total_price = $quantity * $unit;

            if (empty($item->status)) {
                $item->status = 'pending';
            }
        });
    }

    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
