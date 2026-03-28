<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'orders';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'customer_id',
        'address_id',
        'total_amount',
        'status',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'status' => OrderStatusEnum::class,
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function riskAnalysis(): HasOne
    {
        return $this->hasOne(RiskAnalysis::class);
    }
}