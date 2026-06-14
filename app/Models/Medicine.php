<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\DosageForm;
use Database\Factories\MedicineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $category_id
 * @property string $brand_name
 * @property string $scientific_name
 * @property DosageForm $dosage_form
 * @property string $price
 * @property int $stock_quantity
 * @property Carbon|null $expiry_date
 * @property bool $requires_prescription
 * @property bool $is_available
 * @property string|null $description
 * @property string|null $manufacturer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class Medicine extends Model
{
    /** @use HasFactory<MedicineFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'brand_name',
        'scientific_name',
        'dosage_form',
        'price',
        'stock_quantity',
        'expiry_date',
        'requires_prescription',
        'is_available',
        'description',
        'manufacturer',
    ];

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<MedicineImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(MedicineImage::class);
    }

    /**
     * @return HasMany<CartItem, $this>
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dosage_form' => DosageForm::class,
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'expiry_date' => 'date',
            'requires_prescription' => 'boolean',
            'is_available' => 'boolean',
        ];
    }
}
