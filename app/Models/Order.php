<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'canteen_id',
        'customer_id',
        'order_code',
        'customer_name',
        'customer_type',
        'detail_menu_snapshot',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'access_token',
        'checkout_key',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'integer',
            'detail_menu_snapshot' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function canteen(): BelongsTo
    {
        return $this->belongsTo(Canteen::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp' . number_format($this->total_price, 0, ',', '.');
    }

    public function getItemsSummaryAttribute(): string
    {
        $items = is_array($this->detail_menu_snapshot) ? $this->detail_menu_snapshot : json_decode($this->detail_menu_snapshot, true);
        if (!$items) {
            return '-';
        }
        return implode(', ', array_map(fn($item) => ($item['nama_menu'] ?? $item['name'] ?? 'Item') . ' ×' . ($item['jumlah'] ?? $item['qty'] ?? 1), $items));
    }
}
