<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $medicines_count
 */
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
    ];

    /**
     * @return HasMany<Medicine, $this>
     */
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function (Category $category): void {
            if (! isset($category->slug) || $category->slug === '') {
                $category->slug = Str::slug($category->name);
            }
        });

        self::updating(function (Category $category): void {
            if ($category->isDirty('name') && ! $category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
