<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'canteen_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'kategori',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_active', true)->where('stock', '>', 0);
    }

    public function getImageUrlAttribute(): string
    {
        if (str_starts_with($this->image, 'upload:')) {
            $filename = substr($this->image, 7);
            if (file_exists(public_path('assets/uploads/' . $filename))) {
                return asset('assets/uploads/' . $filename);
            }
        }
        if (in_array($this->image, ['ayam.svg', 'mie.svg', 'teh.svg', 'jeruk.svg', 'roti.svg'])) {
            return asset('assets/menu/' . $this->image);
        }
        return asset('assets/menu/default.svg');
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }
}
