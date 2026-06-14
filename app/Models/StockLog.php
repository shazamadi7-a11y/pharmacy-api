<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\StockLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $medicine_id
 * @property int|null $order_id
 * @property string $type
 * @property int $quantity_changed
 * @property int $stock_before
 * @property int $stock_after
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class StockLog extends Model
{
    /** @use HasFactory<StockLogFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'medicine_id',
        'order_id',
        'type',
        'quantity_changed',
        'stock_before',
        'stock_after',
    ];

    /**
     * @return BelongsTo<Medicine, $this>
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_changed' => 'integer',
            'stock_before' => 'integer',
            'stock_after' => 'integer',
        ];
    }
}
