<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $table = 'purchase_requests';

    protected $fillable = [
        'request_number',
        'user_id',
        'department_id',
        'request_date',
        'required_date',
        'priority',
        'status',
        'total_amount',
        'currency',
        'justification',
        'notes',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'erp_request_id',
    ];

    protected $casts = [
        'request_date' => 'date',
        'required_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 'pending',
        'currency' => 'ARS',
    ];

    protected static function booted(): void
    {
        static::creating(function (PurchaseRequest $pr) {
            if (empty($pr->request_number)) {
                // Numeric, timestamp-based to avoid collisions, stays within typical varchar(50)
                // Example: 20251017HHMMSS + 3 random digits
                $base = now()->format('YmdHis');
                $suffix = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
                $pr->request_number = $base . $suffix;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(\App\Models\PurchaseRequestItem::class);
    }

    public function getTotalAmountAttribute($value)
    {
        if ($value !== null) {
            return $value;
        }
        if (array_key_exists('items_sum_total_price', $this->attributes)) {
            return $this->attributes['items_sum_total_price'];
        }
        return $this->items()->sum('total_price');
    }
}
