<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Canteen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seller_id',
        'name',
        'slug',
        'campus_location',
        'description',
        'opening_hours',
        'is_open',
        'logo',
        'banner',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function dailyTransactions(): HasMany
    {
        return $this->hasMany(DailyTransaction::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_open', true);
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && file_exists(public_path('assets/uploads/' . $this->logo))) {
            return asset('assets/uploads/' . $this->logo);
        }
        return asset('assets/kantingo-logo-transparent.png');
    }
}
