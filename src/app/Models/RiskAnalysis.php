<?php

namespace App\Models;

use App\Enums\RiskClassificationEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskAnalysis extends Model
{
    use HasFactory;
    use HasUlids;

    protected $table = 'risk_analyses';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'order_id',
        'score',
        'classification',
        'reasons',
        'analyzed_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'integer',
            'classification' => RiskClassificationEnum::class,
            'reasons' => 'array',
            'analyzed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}